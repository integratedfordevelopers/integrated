<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Database;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Database\DatabaseInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DoctrineODMDatabase implements DatabaseInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        return $this->dm
            ->getConnection()
            ->selectCollection(
                $this->dm->getConfiguration()->getDefaultDB(),
                'content'
            )
            ->find()
            ->getMongoCursor()
            ->batchSize(100)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRow(array $row)
    {
        return $this->dm->getConnection()
            // Use parameters for the database
            ->selectCollection(
                $this->dm->getConfiguration()->getDefaultDB(),
                'content'
            )
            ->update(['_id' => $row['_id']], $row);
    }

    /**
     * @{@inheritdoc}
     */
    public function getObjects()
    {
        return $this->dm
            ->getUnitOfWork()
            ->getDocumentPersister(Content::class)
            ->loadAll()
            ->batchSize(100)
        ;
    }

    /**
     * @param object $object
     */
    public function saveObject($object)
    {
        $this->dm->persist($object);
        $this->dm->flush($object);
    }

    /**
     * @return array
     *
     * @throws \Doctrine\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function getStorageKeys()
    {
        $metadataFactory = $this->dm->getMetadataFactory();
        $allMetadata = $metadataFactory->getAllMetadata();
        $keys = [];

        /** @var ClassMetaData $classMetadata */
        foreach ($allMetadata as $classMetadata) {
            if ($classMetadata->isMappedSuperclass || $classMetadata->isEmbeddedDocument) {
                continue;
            }

            $associations = $classMetadata->getAssociationNames();
            foreach ($associations as $assocFieldName) {
                $assocClassName = $classMetadata->getAssociationTargetClass($assocFieldName);

                if (!$assocClassName) {
                    continue;
                }

                if (StorageInterface::class == $assocClassName || is_subclass_of($assocClassName, StorageInterface::class)) {
                    $items = $this->dm->createQueryBuilder($classMetadata->getName())
                        ->hydrate(false)
                        ->select($assocFieldName.'.identifier')
                        ->field($assocFieldName.'.identifier')->exists(true)
                        ->getQuery()
                        ->toArray();

                    if ($items) {
                        foreach ($items as $item) {
                            $keys[$item[$assocFieldName]['identifier']] = true;
                        }
                    }
                } elseif ($fieldMetaData = $metadataFactory->getMetadataFor($assocClassName)) {
                    $fieldAssociations = $fieldMetaData->getAssociationNames();
                    if ($fieldMetaData instanceof ClassMetadata) {
                        if (!$fieldMetaData->isEmbeddedDocument) {
                            continue;
                        }
                    }

                    foreach ($fieldAssociations as $fieldAssociation) {
                        $fieldAssocClassName = $fieldMetaData->getAssociationTargetClass($fieldAssociation);

                        if (StorageInterface::class == $fieldAssocClassName || is_subclass_of($fieldAssocClassName, StorageInterface::class)) {
                            $items = $this->dm->createQueryBuilder($classMetadata->getName())
                                ->hydrate(false)
                                ->select($assocFieldName.'.'.$fieldAssociation.'.identifier')
                                ->field($assocFieldName.'.'.$fieldAssociation.'.identifier')->exists(true)
                                ->getQuery()
                                ->toArray();

                            if ($items) {
                                foreach ($items as $item) {
                                    if (\is_array($item[$assocFieldName])) {
                                        foreach ($item[$assocFieldName] as $subItem) {
                                            if (isset($subItem[$fieldAssociation]['identifier'])) {
                                                $keys[$subItem[$fieldAssociation]['identifier']] = true;
                                            }
                                        }
                                    } else {
                                        $keys[$item[$assocFieldName][$fieldAssociation]['identifier']] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $keys;
    }
}
