<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Doctrine\ODM\MongoDB\Persister;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PersistenceBuilder
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
     * @param object $document
     * @return array
     */
    public function prepareData($document)
    {
        $class = $this->dm->getClassMetadata(get_class($document));
        $data = [];

        foreach ($class->getFieldNames() as $field) {
            $mapping = $class->getFieldMapping($field);
            $value = $class->reflFields[$mapping['fieldName']]->getValue($document);

            if (!isset($mapping['association'])) {
                // @Field, @String, @Date, etc.
                $data[$mapping['name']] = Type::getType($mapping['type'])->convertToDatabaseValue($value);
            } elseif (isset($mapping['association'])) {
                if ($mapping['association'] === ClassMetadata::REFERENCE_ONE && $mapping['isOwningSide']) {
                    // @ReferenceOne
                    // @todo
                } elseif ($mapping['association'] === ClassMetadata::EMBED_ONE) {
                    // @EmbedOne
                    // @todo
                } elseif ($mapping['type'] === ClassMetadata::MANY && !$mapping['isInverseSide']) {
                    // @ReferenceMany, @EmbedMany
                    // @todo
                }
            }
        }

        if (isset($class->discriminatorField)) {
            $data[$class->discriminatorField] = isset($class->discriminatorValue) ? $class->discriminatorValue : $class->name;
        }

        return $data;
    }
}
