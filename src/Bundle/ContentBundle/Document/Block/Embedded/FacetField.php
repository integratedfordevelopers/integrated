<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Block\Embedded;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * @author Johan Liefers <johan@e-active.nl>
 *
 * @Type\Document("FacetField")
 */
class FacetField
{
    /**
     * @var string
     * @Type\Field
     */
    protected $name;

    /**
     * @var string
     * @Type\Field
     */
    protected $field;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
}
