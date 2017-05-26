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

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\PageBundle\Document\Page\Page;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * TextBlock document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Text block")
 */
class TextBlock extends Block
{
    use PublishTitleTrait;

    /**
     * @var string
     * @Assert\NotBlank
     * @Type\Field(
     *       options={
     *          "attr"={"class"="main-title"}
     *       }
     * )
     */
    protected $title;

    /**
     * @var string
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\EditorType",options={"mode"="web"})
     */
    protected $content;

    /**
     * @var Page|null
     */
    protected $parentPage = null;

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
     * @return Page|null
     */
    public function getParentPage()
    {
        return $this->parentPage;
    }

    /**
     * @param Page|null $parentPage
     * @return $this
     */
    public function setParentPage(Page $parentPage = null)
    {
        $this->parentPage = $parentPage;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'text';
    }
}
