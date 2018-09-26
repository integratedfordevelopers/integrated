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

interface FormConfigIdentifierInterface
{
    /**
     * Get the content type.
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string;
}
