<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Exporter;

use Integrated\Common\Channel\ChannelInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ExporterInterface
{
    /**
     * @param object                  $content
     * @param string                  $state
     * @param string|ChannelInterface $channel
     */
    public function export($content, $state, ChannelInterface $channel);
}
