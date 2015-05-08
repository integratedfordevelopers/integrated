<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Exporter\Queue;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RequestSerializerInterface
{
    /**
     * @param Request $data
     *
     * @return string
     */
    public function serialize(Request $data);

    /**
     * @param string $data
     *
     * @return Request
     */
    public function deserialize($data);
}
