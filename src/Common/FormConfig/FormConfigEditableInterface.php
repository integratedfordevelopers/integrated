<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\FormConfig;

interface FormConfigEditableInterface extends FormConfigInterface
{
    /**
     * Set the name of this config.
     *
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * Set the fields of this config.
     *
     * @param FormConfigFieldInterface[] $fields
     */
    public function setFields(iterable $fields): void;
}
