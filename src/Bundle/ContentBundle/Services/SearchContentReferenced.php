<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Types\Type as MongoType;
use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * Class SearchContentReferenced.
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class SearchContentReferenced
{
    /**
     * @const IGNORE_CLASSES
     */
    const IGNORE_CLASSES = ['Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction'];

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * SearchContentReferenced constructor.
     *
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param mixed $document
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getReferenced($document)
    {
        $metadataFactory = $this->dm->getMetadataFactory();
        $deleted = $this->getDeletedInfo($document, $metadataFactory);
        $allMetadata = $metadataFactory->getAllMetadata();

        $referenced = [];

        /** @var ClassMetaData $classMetadata */
        foreach ($allMetadata as $classMetadata) {
            if (\in_array($classMetadata->getName(), $this::IGNORE_CLASSES)) {
                continue;
            }

            if ($classMetadata->isMappedSuperclass || $classMetadata->isEmbeddedDocument) {
                continue;
            }

            $associations = $classMetadata->getAssociationNames();
            foreach ($associations as $assocFieldName) {
                $assocClassName = $classMetadata->getAssociationTargetClass($assocFieldName);

                if (!$assocClassName) {
                    continue; // Skip empty class
                }

                if ($deleted['className'] == $assocClassName || is_subclass_of($deleted['className'], $assocClassName)) {
                    $items = $this->dm->createQueryBuilder($classMetadata->getName())
                        ->field($assocFieldName.'.$id')
                        ->equals($deleted['idValue'])
                        ->getQuery()
                        ->toArray();

                    if ($items) {
                        foreach ($items as $item) {
                            $referenced[] = $item;
                        }
                    }
                } elseif ($fieldMetaData = $metadataFactory->getMetadataFor($assocClassName)) {
                    $fieldAssociations = $fieldMetaData->getAssociationNames();

                    foreach ($fieldAssociations as $fieldAssociation) {
                        $fieldAssocClassName = $fieldMetaData->getAssociationTargetClass($fieldAssociation);
                        $allow = $deleted['className'] == $fieldAssocClassName || is_subclass_of($deleted['className'], $fieldAssocClassName);

                        if (!$fieldMetaData->isEmbeddedDocument) {
                            $allow = $deleted['className'] != $fieldAssocClassName && is_subclass_of($deleted['className'], $fieldAssocClassName);
                        }

                        if ($allow) {
                            $items = $this->dm->createQueryBuilder($classMetadata->getName())
                                ->field($assocFieldName.'.'.$fieldAssociation.'.$id')
                                ->equals($deleted['idValue'])
                                ->getQuery()
                                ->toArray();

                            if ($items) {
                                foreach ($items as $item) {
                                    $referenced[] = $item;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->prepareReferenced($referenced);
    }

    /**
     * @param mixed                $document
     * @param ClassMetadataFactory $metadataFactory
     *
     * @return array
     *
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Exception
     */
    public function getDeletedInfo($document, ClassMetadataFactory $metadataFactory)
    {
        $deleted = [
            'className' => \get_class($document),
            'metadata' => $metadataFactory->getMetadataFor(\get_class($document)),
        ];

        $deleted['idField'] = current($deleted['metadata']->getIdentifier());
        $deleted['idValue'] = $deleted['metadata']->getFieldValue($document, $deleted['idField']);

        if (MongoType::hasType($deleted['metadata']->getTypeOfField($deleted['idField']))) {
            $typeClass = MongoType::getType($deleted['metadata']->getTypeOfField($deleted['idField']));
            $deleted['idValue'] = $typeClass->convertToDatabaseValue($deleted['idValue']);
        } else {
            throw new \Exception('The identifer of the deleted object must have a valid Doctrine field type');
        }

        return $deleted;
    }

    /**
     * @param $referenced
     *
     * @return array
     */
    private function prepareReferenced($referenced)
    {
        $output = [];
        foreach ($referenced as $item) {
            $key = \get_class($item).'-'.$item->getId();
            if ($item instanceof Content) {
                $output[$key] = [
                    'action' => 'integrated_content_content_edit',
                    'id' => $item->getId(),
                    'name' => method_exists($item, 'getTitle') ? $item->getTitle() : \get_class($item),
                ];
            } else {
                $output[$key] = [
                    'id' => $item->getId(),
                    'name' => \get_class($item),
                ];
            }
        }

        return $output;
    }
}
