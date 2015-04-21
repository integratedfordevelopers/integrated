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

use Integrated\Common\Channel\Connector\Adaptor\ManifestInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface AdaptorInterface
{
    /**
     * @return ManifestInterface
     */
    public function getManifest();

//    /**
//     * @return ConfigInterface
//     */
//    public function getConfig();
}
