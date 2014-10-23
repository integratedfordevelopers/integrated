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
   	private $accessor;

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
        foreach ($options as $field => $path) {
            $this->remove($container, $field);

            foreach ($this->read($data, $path) as $value) {
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
     * @param object         $data
     * @param string | array $path
     *
     * @return array
     */
    protected function read($data, $path)
    {
        if (!is_array($path)) {
            return [$this->readScalar($data, $path)];
        }

        return $this->readArray($data, $path, ' ');
    }

    /**
     * @param object $data
     * @param array  $path
     * @param string $separator
     *
     * @return array
     */
    protected function readArray($data, array $path, $separator = ' ')
    {
        $separator = $this->extractSeparator($path, $separator);
        $extracted = [];

        foreach ($path as $key => $path_child) {
            if (is_array($path_child)) {
                try {
                    $array = $this->accessor->getValue($data, (string) $key);

                    if (!is_array($array) && !$array instanceof Traversable) {
                        $array = [$array];
                    }
                } catch (ExceptionInterface $e) {
                    $array = [];
                }

                $results = [];

                foreach ($array as $array_data) {
                    if ($result = $this->readArray($array_data, $path_child, $separator)) {
                        if (is_array($result)) {
                            $results = array_merge($results, $result);
                        } else {
                            $results[] = $result;
                        }
                    }
                }

                $extracted[] = $results;
            } else {
                $extracted[] = ($path_child[0] == '@') ? $this->readScalar($data, substr($path_child, 1)) : $path;
            }
        }

        $results = array_shift($extracted);
        $results = is_array($results) ? $results : [$results];

        while ($value = array_shift($extracted)) {
            if (is_array($value)) {
                $replacement = [];

                foreach ($value as $array_value) {
                    foreach ($results as $result_value) {
                        $replacement = $result_value . $separator . $array_value;
                    }
                }

                $results = $replacement;
            } else {
                foreach ($results as $key => $result_value) {
                    $results[$key] = $result_value . $separator . $value;
                }
            }
        }

        return $results;
    }

    /**
     * @param mixed  $data
     * @param string $path
     *
     * @return null | string
     */
    protected function readScalar($data, $path)
    {
        try {
            $value = $this->accessor->getValue($data, (string) $path);
        } catch (ExceptionInterface $e) {
            return null;
        }

        if ($value instanceof DateTime) {
            $value = clone $value; // don't change to original value
            return $value->setTimezone($this->timezone)->format('Y-m-d\TG:i:s\Z');
        }

        if (is_object($value) && !method_exists($value, '__toString')) {
            return null; // cant convert to a string so ignore.
        }

        return $value !== null ? (string) $value : null;
    }

    /**
     * @param array  $path
     * @param string $default
     *
     * @return string
     */
    protected function extractSeparator(array &$path, $default)
    {
        $separator = $default;

        if (array_key_exists('separator', $path) && !is_array($path['separator'])) {
            $separator = (string) $path['separator'];
            unset($path['separator']);
        }

        return $separator;
    }
}
