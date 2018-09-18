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

use Integrated\Common\ContentType\ContentTypeInterface;

interface FormConfigFactoryInterface
{
    /**
     * @param ContentTypeInterface $type
     * @param string               $key
     *
     * @return FormConfigEditableInterface
     */
    public function create(ContentTypeInterface $type, string $key): FormConfigEditableInterface;
}
