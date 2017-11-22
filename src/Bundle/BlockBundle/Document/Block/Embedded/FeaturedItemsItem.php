<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\BlockBundle\Document\Block\Embedded;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * @author Johan Liefers <johan@e-active.nl>
 *
 * @Type\Document("FeaturedItemsItem")
 */
class FeaturedItemsItem
{
    /**
     * @var string
     * @Type\Field
     */
    protected $title;

    /**
     * @var StorageInterface
     * @Type\Field(type="Integrated\Bundle\StorageBundle\Form\Type\ImageType")
     */
    protected $image;

    /**
     * @var string
     * @Type\Field
     */
    protected $link;

    /**
     * @var string
     * @Type\Field
     */
    protected $linkText;

    /**
     * @var string
     * @Type\Field(
     *      type="choice",
     *      options={
     *          "label"="Link target",
     *          "expanded"=true,
     *          "choices"={
     *               "_self"="Current window",
     *               "_blank"="New window"
     *          }
     *      }
     *  )
     */
    protected $target = '_self';

    /**
     * @var string
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\EditorType",options={"mode"="web"})
     */
    protected $text;

    /**
     * @var bool
     * @Type\Field(
     *      type="Symfony\Component\Form\Extension\Core\Type\CheckboxType",
     *      options={
     *          "required"=false,
     *          "attr"={"align_with_widget"=true}
     *      }
     * )
     */
    protected $disabled = false;

    /**
     * @var int
     * @Type\Field(type="hidden", options={"attr"={"data-itemorder"="collection"}})
     */
    protected $order;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param StorageInterface $image
     * @return $this
     */
    public function setImage(StorageInterface $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkText()
    {
        return $this->linkText;
    }

    /**
     * @param string $linkText
     * @return $this
     */
    public function setLinkText($linkText)
    {
        $this->linkText = $linkText;
        return $this;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param boolean $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = (int) $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
}
