<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ConfigInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getAdapter();

    /**
     * @return OptionsInterface
     */
    public function getOptions();
}
