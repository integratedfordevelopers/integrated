<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\ContentType\Embedded;

use Integrated\Common\ContentType\ContentTypeFieldInterface;

/**
 * Embedded document Field.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Field implements ContentTypeFieldInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the field.
     *
     * @param string $name The name of the property of the content type
     *
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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the options of the field.
     *
     * @param array $options The options of the form field
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Added shortcut to getLabel of field.
     *
     * @return string
     *
     * @deprecated since version 0.7, this object does not contain all the options
     *             so the label can not be determined based solely on this object.
     */
    public function getLabel()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 0.7.', \E_USER_DEPRECATED);

        return isset($this->options['label']) ? $this->options['label'] : ucfirst($this->getName());
    }
}
