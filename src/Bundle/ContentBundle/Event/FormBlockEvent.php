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

use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\Event;

class FormBlockEvent extends Event
{
    const PRE_LOAD = 'form_block.pre_load';

    const PRE_FLUSH = 'form_block.pre_flush';

    const POST_FLUSH = 'form_block.post_flush';

    /**
     * @var string
     */
    protected $content;

    /**
     * @param ContentInterface $content
     */
    public function __construct($content)
    {
        $this->content = $content;
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
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
