<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Fixtures;

use ArrayObject;
use DateTime;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TestObject
{
    /**
     * @var DateTime
     */
    public $datetime;

    /**
     * @var bool
     */
    public $bool0 = false;
    /**
     * @var bool
     */
    public $bool1 = true;
    /**
     * @var int
     */
    public $int   = 42;
    /**
     * @var float
     */
    public $float = 4.2;

    /**
     * @var string
     */
    protected $field1 = 'field1';
    /**
     * @var string
     */
    protected $field2 = 'field2';
    /**
     * @var string
     */
    protected $field3 = 'field3';
    /**
     * @var string
     */
    protected $field4 = 'field4';

    /**
     * @var ArrayObject
     */
    public $arrayObject;

    /**
     * FieldMapperTypeTestTestObject constructor.
     */
    public function __construct()
    {
        $this->datetime = new DateTime('2014-01-01 00:30 CET');

        $this->arrayObject = new ArrayObject([
            'field1' => 'field1',
            'field2' => 'field2',
            'field3' => 'field3',

            'array1' => new ArrayObject([
                'field1' => 'array1.1',
                'field2' => 'array1.2',
                'field3' => 'array1.3'
            ], ArrayObject::ARRAY_AS_PROPS),

            'array2' => new ArrayObject([
                'field1' => 'array2.1',
                'field2' => 'array2.2',
                'field3' => 'array2.3'
            ], ArrayObject::ARRAY_AS_PROPS)
        ], ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function getField1()
    {
        return $this->field1;
    }

    /**
     * @return string
     */
    public function getField2()
    {
        return $this->field2;
    }

    /**
     * @return string
     */
    public function getField3()
    {
        return $this->field3;
    }

    /**
     * @return string
     */
    public function getField4()
    {
        return $this->field4;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return [];
    }

    /**
     * @return $this
     */
    public function getSelf()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '__toString';
    }
}
