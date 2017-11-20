<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConverterSpecification implements ConverterSpecificationInterface
{
    public $classes = array();

    public $fields = array();

    public $id = null;

    public function hasClass($class)
    {
        return in_array($class, $this->classes);
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function hasField($field)
    {
        return array_key_exists($field, $this->fields);
    }

    public function getField($field)
    {
        return $this->fields[$field];
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getId()
    {
        return $this->id;
    }
}
