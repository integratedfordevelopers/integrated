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

interface FormConfigFieldInterface
{
    /**
     * Get the name of the document.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the type of the document.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get the options of the document.
     *
     * @return array
     */
    public function getOptions(): array;
}
