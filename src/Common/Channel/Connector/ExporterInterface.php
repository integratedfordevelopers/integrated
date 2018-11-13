<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector;

use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Exporter\ExporterResponse;

interface ExporterInterface
{
    const STATE_ADD = 'add';

    const STATE_DELETE = 'delete';

    /**
     * @param object                    $content
     * @param string                    $state
     * @param string | ChannelInterface $channel
     *
     * @return ExporterResponse
     */
    public function export($content, $state, ChannelInterface $channel);
}
