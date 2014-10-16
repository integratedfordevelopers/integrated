<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config\Provider;

use Exception;

use Integrated\Common\Converter\Config\TypeConfig;
use Integrated\Common\Converter\Config\TypeConfigInterface;
use Integrated\Common\Converter\Exception\RuntimeException;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use SimpleXMLElement;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class XmlProvider extends AbstractFileProvider
{
    /**
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        parent::__construct($finder, 'xml');
    }

    /**
     * {@inheritdoc}
     */
    protected function load(SplFileInfo $file)
    {
        $types = [];

        // There should be a xsd to validate the xml so we can assume the xml is valid when it is returned
        // by getElement. So no need to check if field exist or not and if there are in the correct order
        // or not.

        foreach ($this->getElement($file)->class as $class) {
            $name = (string) $class['name'];

            if (!isset($types[$name])) {
                $types[$name] = [];
            }

            $types[$name] = array_merge($types[$name], $this->parseTypes($class));
        }

        return $types;
    }

    /**
     * @param SimpleXMLElement $element
     * @return TypeConfigInterface[]
     */
    protected function parseTypes(SimpleXMLElement $element)
    {
        $types = [];

        foreach ($element->type as $type) {
            $options = null;

            if (isset($type->options)) {
                $options = $this->parseOptions($type->options[0]);
            }

            $types[] = new TypeConfig((string) $type['name'], $options);
        }

        return $types;
    }

    /**
     * @param SimpleXMLElement $element
     * @return array
     */
    protected function parseOptions(SimpleXMLElement $element)
    {
        if (!$element->count()) {
            return []; // empty array if options contains no data
        }

        $child = $element->children();
        $child = $child[0]; // can only contain 1 child

        return $this->parsePrimitive($child);
    }

    /**
     * @param SimpleXMLElement $element
     * @return null | array | string | int | float | bool
     */
    protected function parsePrimitive(SimpleXMLElement $element)
    {
        switch ($element->getName()) {
            case 'array';
                $result = [];

                foreach ($element->children() as $child) {
                    if (isset($child['key'])) {
                        $result[(string) $child['key']] = $this->parsePrimitive($child);
                    } else {
                        $result[] = $this->parsePrimitive($child);
                    }
                }

                return $result;

            case 'string':
                return (string) $element;

            case 'int':
                return (int)(string) $element;

            case 'float':
                return (float)(string) $element;

            case 'bool':
                $result = (string) $element;

                if (strcasecmp('false', $result) === 0) {
                    return false;
                }

                return (bool) $result;
        }

        return null;
    }

    /**
     * @param SplFileInfo $file
     * @return SimpleXMLElement
     *
     * @trows RuntimeException if file can not be read or parsed
     */
    protected function getElement(SplFileInfo $file)
    {
        $previous = libxml_use_internal_errors(true);

        $error = null;
        $xml = null;

        try {
            if (!$xml = simplexml_load_string($file->getContents())) {
                $error = libxml_get_last_error();
                $error = $error->message;
            }
        }
        catch (Exception $e) {
            $error = $e->getMessage();
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$xml) {
            throw new RuntimeException(sprintf('Unable to parse "%s" as the file contains errors "%s".', $file->getPathname(), $error));
        }

        return $xml;
    }
}
