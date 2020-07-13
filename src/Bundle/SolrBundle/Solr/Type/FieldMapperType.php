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

use AppendIterator;
use ArrayIterator;
use DateTime;
use DateTimeZone;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Traversable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FieldMapperType implements TypeInterface
{
    /**
     * @var DateTimeZone
     */
    private $timezone;

    /**
     * @var PropertyAccessorInterface
     */
    protected $accessor;

    /**
     * Constructor.
     *
     * @param PropertyAccessorInterface $accessor
     */
    public function __construct(PropertyAccessorInterface $accessor = null)
    {
        $this->timezone = new DateTimeZone('UTC');
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        foreach ($this->groupFields($options) as $field => $config) {
            $this->remove($container, $field);

            foreach ($this->read($data, $config) as $value) {
                $this->append($container, $field, $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.fields';
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function groupFields(array $options = [])
    {
        $fields = [];

        // first group the options by field name

        foreach ($options as $field => $value) {
            if (\is_array($value)) {
                $field = $value[$index = isset($value['name']) ? 'name' : key($value)];
                unset($value[$index]);
            } else {
                $value = ['@'.$value]; // convert simple config to advance config
            }

            foreach ($value as $config) {
                $fields[$field][] = $config;
            }
        }

        return $fields;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $field
     */
    protected function remove(ContainerInterface $container, $field)
    {
        $container->remove($field);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $field
     * @param string             $value
     */
    protected function append(ContainerInterface $container, $field, $value)
    {
        if ($value === null) {
            return;
        }

        $value = trim($value);
        $value = preg_replace('/\s+/u', ' ', $value);

        $container->add($field, $value);
    }

    /**
     * @param object $data
     * @param array  $paths
     *
     * @return Traversable
     */
    protected function read($data, array $paths)
    {
        $result = new AppendIterator();

        foreach ($paths as $path) {
            if (\is_array($path)) {
                $result->append(new ArrayIterator($this->readArray($data, $path)));
            } else {
                $result->append(new ArrayIterator([$this->readString($data, $path)]));
            }
        }

        return $result;
    }

    /**
     * @param mixed  $data
     * @param array  $paths
     * @param string $separator
     *
     * @return string[]
     */
    protected function readArray($data, array $paths, $separator = ' ')
    {
        $extracted = [];

        // Check if there is a separator in the path config and if so extract it and then remove it
        // from the path config.

        if (\array_key_exists('separator', $paths) && !\is_array($paths['separator'])) {
            $separator = (string) $paths['separator'];
            unset($paths['separator']);
        }

        foreach ($paths as $index => $path) {
            if (\is_array($path)) {
                // Since $path is a array the $index with be treated as a path and the result of that
                // path is treated as a array. If the result is not a array then it will be placed in
                // a array to simulate that the result is a array.

                try {
                    $array = $this->accessor->getValue($data, (string) $index);

                    if (!\is_array($array) && !$array instanceof Traversable) {
                        $array = [$array];
                    }
                } catch (ExceptionInterface $e) {
                    $array = [];
                }

                $results = [];

                foreach ($array as $value) {
                    if ($path) {
                        $results = array_merge($results, $this->readArray($value, $path, $separator));
                    } else {
                        if ($value = $this->convert($value)) {
                            $results[] = $value;
                        }
                    }
                }

                $extracted[] = $results;
            } else {
                $extracted[] = $this->readString($data, $path);
            }
        }

        // The data is extracted so now its time to combine all the data into strings.

        return $this->combine($extracted, $separator);
    }

    /**
     * @param mixed  $data
     * @param string $path
     */
    protected function readString($data, $path)
    {
        $path = (string) $path;

        if (!$path) {
            return null;
        }

        if ($path[0] != '@') {
            return $path; // static string
        }

        $path = substr($path, 1);

        // Use the property accessor so extract the value from the data. If the path does not exist in the
        // data then don't return a error but just null.

        try {
            return $this->convert($this->accessor->getValue($data, (string) $path));
        } catch (ExceptionInterface $e) {
            return null;
        }
    }

    /**
     * Convert the data to a string.
     *
     * If the value can not be converted to a string then return null.
     *
     * @param mixed $data
     */
    protected function convert($data)
    {
        if ($data instanceof DateTime) {
            $data = clone $data; // don't change to original value

            return $data->setTimezone($this->timezone)->format('Y-m-d\TG:i:s\Z');
        }

        if (\is_object($data) && !method_exists($data, '__toString')) {
            return null; // can't convert object to a string.
        }

        if (\is_array($data)) {
            return null; // can't convert a array to a string.
        }

        if (\is_bool($data)) {
            return $data ? '1' : '0'; // would otherwise be empty if converted to a string
        }

        return $data !== null ? (string) $data : null;
    }

    /**
     * Combine all the data into strings.
     *
     * For every array in the data all strings will be multiplied by the number of items in that
     * array to cover every possible string combination.
     *
     * @param array  $data
     * @param string $separator
     *
     * @return string[]
     */
    protected function combine(array $data, $separator)
    {
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        $results = array_shift($data);
        $results = \is_array($results) ? $results : [$results];

        while ($value = array_shift($data)) {
            if (\is_array($value)) {
                $replacement = [];

                foreach ($value as $array_value) {
                    foreach ($results as $result_value) {
                        $replacement[] = $result_value.$separator.$array_value;
                    }
                }

                $results = $replacement;
            } else {
                foreach ($results as $key => $result_value) {
                    $results[$key] = $result_value.$separator.$value;
                }
            }
        }

        return array_filter($results);
    }
}
