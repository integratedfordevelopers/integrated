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

use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

/**
 * Embedded document CustomField
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomField extends Field
{
    /**
     * {@inheritdoc}
     * @Slug(fields={"getLabel"})
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @return string
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
}
