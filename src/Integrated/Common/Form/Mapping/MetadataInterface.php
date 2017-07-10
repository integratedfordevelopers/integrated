<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Mapping;

use ReflectionClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface MetadataInterface
{
    /**
     * Checks if the document is a type of the given class or interface
     *
     * @param string $class
     * @return bool
     */
    public function isTypeOf($class);

    /**
     * @return ReflectionClass
     */
    public function getReflection();

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return AttributeInterface[]
     */
    public function getFields();

    /**
     * @param string $name
     * @return AttributeInterface
     */
    public function getField($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasField($name);

    /**
     * @return AttributeInterface[]
     */
    public function getOptions();

    /**
     * @param string $name
     * @return AttributeInterface
     */
    public function getOption($name);

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name);
}
