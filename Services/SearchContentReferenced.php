<?php

namespace Integrated\Bundle\ContentBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Types\Type as MongoType;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\ContentBundle\Document\Content\Article;

/**
 * Class SearchContentReferenced
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class SearchContentReferenced
{
    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * SearchContentReferenced constructor.
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param mixed $document
     * @return bool
     * @throws \Exception
     */
    public function getReferenced($document)
    {
        $metadataFactory = $this->dm->getMetadataFactory();
        $deleted = $this->getDeletedInfo($document, $metadataFactory);
        $allMetadata = $metadataFactory->getAllMetadata();

        $refereced = [];

        /** @var ClassMetaData  $classMetadata */
        foreach ($allMetadata as $classMetadata) {
            if ($classMetadata->isMappedSuperclass || $classMetadata->isEmbeddedDocument) {
                continue;
            }

            $associations = $classMetadata->getAssociationNames();
            foreach ($associations as $assocFieldName) {
                $assocClassName = $classMetadata->getAssociationTargetClass($assocFieldName);

                if ($deleted['className'] == $assocClassName) {
                    $items = $this->dm->createQueryBuilder($classMetadata->getName())
                        ->field($assocFieldName.'.$id')
                        ->equals($deleted['idValue'])
                        ->getQuery()
                        ->toArray();

                    if ($items) {
                        foreach ($items as $item) {
                            $refereced[] = $item;
                        }
                    }
                }
            }
        }

        $items = $this->dm->createQueryBuilder('IntegratedContentBundle:Content\Content')
            ->field('relations.references.$id')
            ->equals($deleted['idValue'])
            ->getQuery()
            ->toArray();

        if ($items) {
            foreach ($items as $item) {
                $refereced[] = $item;
            }
        }

        return $this->prepareReferenced($refereced);
    }

    /**
     * @param mixed                $document
     * @param ClassMetadataFactory $metadataFactory
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Exception
     */
    public function getDeletedInfo($document, ClassMetadataFactory $metadataFactory)
    {
        $deleted = array(
            'className' => get_class($document),
            'metadata'  => $metadataFactory->getMetadataFor(get_class($document)),
        );

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
     * @param $refereced
     * @return array
     */
    private function prepareReferenced($refereced)
    {
        $output = [];
        foreach ($refereced as $item) {
            if ($item instanceof Article) {
                $output[] = [
                    'action' => 'integrated_content_content_edit',
                    'id' => $item->getId(),
                    'name' => $item->getTitle(),
                ];
            } else {
                $output[] = [
                    'id' => $item->getId(),
                    'name' => get_class($item),
                ];
            }
        }

        return $output;
    }
}
