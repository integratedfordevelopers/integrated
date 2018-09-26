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

interface FormConfigInterface
{
    /**
     * Get the id of this config.
     *
     * @return FormConfigIdentifierInterface
     */
    public function getId(): FormConfigIdentifierInterface;

    /**
     * Get the name of this config.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the fields of this config.
     *
     * @return FormConfigFieldInterface[]
     */
    public function getFields(): array;
}
