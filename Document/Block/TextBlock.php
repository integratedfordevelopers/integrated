<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\WebsiteBundle\Document\Block;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * TextBlock document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Text block")
 */
class TextBlock extends Block
{
    /**
     * @var string
     * @ODM\String
     * @Type\Field(type="integrated_tinymce")
     */
    protected $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
