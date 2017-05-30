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

/**
 * @author Johan Liefers <johan@e-active.nl>
 *
 * @Type\Document("Inline block")
 */
class InlineBlock extends Block
{
    /**
     * @var string
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\EditorType",options={"mode"="web"})
     */
    protected $content;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \DateTime
     */
    protected $publishedAt;

    /**
     * @var \DateTime
     */
    protected $publishedUntil;

    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        parent::__construct();

        $this->page = $page;
        $this->title = 'inline block';
        $this->layout = 'default.html.twig';
    }

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
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'inline';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId();
    }
}
