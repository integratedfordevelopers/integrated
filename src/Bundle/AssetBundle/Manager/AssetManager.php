<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Manager;

use Integrated\Bundle\AssetBundle\Asset\Asset;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class AssetManager
{
    const MODE_APPEND = 'append';
    const MODE_PREPEND = 'prepend';

    /**
     * @var array
     */
    private $assets = [];

    /**
     * @var array
     */
    private $hash = [];

    /**
     * @param string|array $asset
     * @param bool         $inline
     * @param string       $mode
     *
     * @throws \InvalidArgumentException
     */
    public function add($asset, $inline = false, $mode = self::MODE_APPEND)
    {
        if (!\in_array($mode, [self::MODE_APPEND, self::MODE_PREPEND])) {
            throw new \InvalidArgumentException(sprintf('Invalid mode "%s".', $mode));
        }

        $function = self::MODE_PREPEND === $mode ? 'array_unshift' : 'array_push';

        foreach ((array) $asset as $content) {
            $hash = crc32(trim($content));

            if (isset($this->hash[$hash])) {
                // skip duplicate
                continue;
            }

            $this->hash[$hash] = 1;

            $function($this->assets, new Asset($content, $inline));
        }
    }

    /**
     * @return Asset[]
     */
    public function getAssets()
    {
        return $this->assets;
    }
}
