<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Document type Form.
 *
 * @Type\Document("Form")
 */
class Form extends Content
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Form created at '.strftime('F jS, Y', $this->createdAt->getTimestamp());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
