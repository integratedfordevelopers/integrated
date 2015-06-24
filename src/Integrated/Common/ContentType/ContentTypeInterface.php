<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface ContentTypeInterface
{
    /**
     * Get the id of the content type
     *
     * @return string
     */
    public function getId();

    /**
     * Get the class of the content type
     *
     * @return string
     */
    public function getClass();

    /**
     * Get the name of the content type
     *
     * @return string
     */
    public function getName();

    /**
     * Get the type of the content type
     *
     * @return string
     */
    public function getType();

    /**
     * Get a new instance of this content type
     *
     * @return ContentInterface
     */
    public function create();

    /**
     * Get all the fields of this content type, the field are returned in order.
     *
     * @return ContentTypeFieldInterface[]
     */
    public function getFields();

    /**
     * Get the information of the specified field
     *
     * @param string $name The field name
     * @return ContentTypeFieldInterface
     */
    public function getField($name);

    /**
     * Check if a field exist in the content type
     *
     * @param $name
     * @return bool
     */
    public function hasField($name);

    /**
     * Get all the options of this content type.
     *
     * @return mixed[]
     */
    public function getOptions();

    /**
     * Get the value of the specified key.
     *
     * @param string $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * Check if the option exist in the content type.
     *
     * @param string $name
     * @return bool
     */
    public function hasOption($name);
}
