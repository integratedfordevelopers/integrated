<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Form;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ConfigInterface
{
    /**
     * @return string
     */
    public function getHandler();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @return mixed
     */
    public function getMatcher();
}
