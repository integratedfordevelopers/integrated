<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Asset;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Asset
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var bool
     */
    protected $inline;

    /**
     * @param string $content
     * @param bool   $inline
     */
    public function __construct($content, $inline = false)
    {
        $this->content = $content;
        $this->inline = (bool) $inline;
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
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInline()
    {
        return $this->inline;
    }

    /**
     * @param bool $inline
     *
     * @return $this
     */
    public function setInline($inline)
    {
        $this->inline = (bool) $inline;

        return $this;
    }
}
