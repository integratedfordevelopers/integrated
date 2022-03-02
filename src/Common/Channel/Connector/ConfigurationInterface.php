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

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ConfigurationInterface
{
    /**
     * Get the configuration form.
     *
     * This can be a string or a form type object.
     *
     * @return string|FormTypeInterface
     */
    public function getForm();
}
