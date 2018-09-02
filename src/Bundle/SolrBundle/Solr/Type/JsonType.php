<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Solr\Type;

use Integrated\Common\Converter\ContainerInterface;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Traversable;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class JsonType extends FieldMapperType
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        foreach ($this->groupFields($options) as $field => $config) {
            $this->remove($container, $field);

            if (\is_array($config)) {
                foreach ($this->readValues($data, $config) as $values) {
                    foreach ($values as $value) {
                        $this->append($container, $field, json_encode($value));
                    }
                }
            }
        }
    }

    /**
     * @param mixed $data
     * @param array $paths
     *
     * @return array
     */
    protected function readValues($data, array $paths)
    {
        $extracted = [];

        foreach ($paths as $index => $path) {
            $index = (string) $index;

            if (\is_array($path)) {
                try {
                    $array = $this->accessor->getValue($data, $index);

                    if (!\is_array($array) && !$array instanceof Traversable) {
                        $array = [$array];
                    }
                } catch (ExceptionInterface $e) {
                    $array = [];
                }

                $results = [];

                foreach ($array as $value) {
                    if ($path) {
                        $results[] = $this->readValues($value, $path);
                    } else {
                        if ($value = $this->convert($value)) {
                            $results[] = $value;
                        }
                    }
                }

                $extracted[$index] = $results;
            } else {
                $extracted[$index] = $this->readString($data, $path);
            }
        }

        return $extracted;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.json';
    }
}
