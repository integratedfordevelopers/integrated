<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\MongoDB\ContentType\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\ContentTypeFieldInterface;

/**
 * Embedded document Field
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Field implements ContentTypeFieldInterface
{
    /**
     * @var string The name of the property of the content type
     * @ODM\String
     */
    protected $name;

    /**
     * @var string The type of the form field
     * @ODM\String
     */
    protected $type;

    /**
     * @var string The label of the form field
     * @ODM\String
     */
    protected $label;

    /**
     * @var bool Is the form field required
     * @ODM\Boolean
     */
    protected $required;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the field
     *
     * @param string $name The name of the property of the content type
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the field
     *
     * @param string $type The type of the form field
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label of the field
     *
     * @param string $label The label of the form field
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get the required of the field
     *
     * @return bool Is the form field required
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set the required of the field
     *
     * @param bool $required Is the form field required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }
}