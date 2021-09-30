<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Event;

use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Content\ContentInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FormBlockEvent extends Event
{
    const PRE_LOAD = 'form_block.pre_load';

    const PRE_FLUSH = 'form_block.pre_flush';

    const POST_FLUSH = 'form_block.post_flush';

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var BlockInterface|null
     */
    protected $block;

    /**
     * @param ContentInterface $content
     * @param null             $block
     */
    public function __construct($content, $block = null)
    {
        $this->content = $content;
        $this->block = $block;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return BlockInterface|null
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param BlockInterface|null $block
     *
     * @return $this
     */
    public function setBlock(?BlockInterface $block)
    {
        $this->block = $block;

        return $this;
    }
}
