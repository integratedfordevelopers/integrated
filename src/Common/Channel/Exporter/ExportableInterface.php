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

use Integrated\Common\Channel\Connector\Config\ConfigInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ExportableInterface
{
    /**
     * @param ConfigInterface $config
     *
     * @return ConfigInterface
     */
    public function getExporter(ConfigInterface $config);
}
