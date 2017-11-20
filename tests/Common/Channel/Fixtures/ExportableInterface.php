<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Channel\Fixtures;

use Integrated\Common\Channel\Connector\AdapterInterface;
use Integrated\Common\Channel\Exporter\ExportableInterface as BaseExportableInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ExportableInterface extends BaseExportableInterface, AdapterInterface
{
}
