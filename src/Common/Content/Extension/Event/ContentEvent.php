<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Event;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Extension\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentEvent extends Event
{
    /**
     * @var mixed
     */
    private $data = null;

    /**
     * @var ContentInterface
     */
    private $content;

    public function __construct(ContentInterface $content)
    {
        parent::__construct(self::CONTENT);

        $this->content = $content;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }
}
