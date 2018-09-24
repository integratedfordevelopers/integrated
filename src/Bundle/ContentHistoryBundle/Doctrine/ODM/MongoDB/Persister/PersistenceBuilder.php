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

use Doctrine\Common\Collections\Collection;
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
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public function prepareData($document)
    {
        if (!\is_object($document)) {
            throw new \RuntimeException('The given argument should be an object.');
        }

        $class = $this->dm->getClassMetadata(\get_class($document));
        $data = [];

        foreach ($class->getFieldNames() as $field) {
            $mapping = $class->getFieldMapping($field);
            $value = $class->reflFields[$mapping['fieldName']]->getValue($document);

            if (!isset($mapping['association'])) {
                // @Field, @String, @Date, etc.
                $value2 = Type::getType($mapping['type'])->convertToDatabaseValue($value);

                if ($value2 instanceof \stdClass) {
                    $value2 = (array) $value2;
                }

                $data[$mapping['name']] = $value2;
            } elseif (isset($mapping['association'])) {
                if ($mapping['association'] === ClassMetadata::REFERENCE_ONE && $mapping['isOwningSide']) {
                    // @ReferenceOne
                    $data[$mapping['name']] = \is_object($value) ? $this->dm->createDBRef($value, $mapping) : null;
                } elseif ($mapping['association'] === ClassMetadata::EMBED_ONE) {
                    // @EmbedOne
                    $data[$mapping['name']] = \is_object($value) ? $this->prepareData($value) : null;
                } elseif ($mapping['type'] === ClassMetadata::MANY && !$mapping['isInverseSide']) {
                    // @ReferenceMany, @EmbedMany
                    if (!$value instanceof Collection) {
                        continue;
                    }

                    if ($value->isEmpty()) {
                        $data[$mapping['name']] = [];
                    }

                    foreach ($value as $object) {
                        if (!\is_object($object)) {
                            continue;
                        }

                        if ($mapping['association'] === ClassMetadata::REFERENCE_MANY) {
                            $data[$mapping['name']][] = $this->dm->createDBRef($object, $mapping);
                        } else {
                            $data[$mapping['name']][] = $this->prepareData($object);
                        }
                    }
                }
            }
        }

        if (isset($class->discriminatorField)) {
            $data[$class->discriminatorField] = isset($class->discriminatorValue) ? $class->discriminatorValue : $class->name;
        }

        return $data;
    }
}
