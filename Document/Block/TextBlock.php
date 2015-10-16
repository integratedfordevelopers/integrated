<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Document\Block;

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
     * @var string
     * @ODM\String
     * @Type\Field(options={"required"=false})
     */
    protected $publishedTitle;

    /**
     * @ODM\Boolean
     * @Type\Field(
     *      type="checkbox",
     *      options={
     *          "required"=false
     *      }
     * )
     */
    protected $useTitle;

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

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getPublishedTitle()
    {
        return $this->publishedTitle;
    }

    /**
     * @param string $publishedTitle
     */
    public function setPublishedTitle($publishedTitle)
    {
        $this->publishedTitle = $publishedTitle;
    }

    public function getValidTitle()
    {
        if ($this->useTitle) {
            return $this->title;
        }

        return $this->publishedTitle != null ? $this->publishedTitle : $this->title;
    }

    /**
     * @return boolean
     */
    public function getUseTitle()
    {
        return $this->useTitle;
    }

    /**
     * @param boolean $useTitle
     */
    public function setUseTitle($useTitle)
    {
        $this->useTitle = $useTitle;
    }
}
