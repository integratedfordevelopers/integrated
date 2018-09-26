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

interface FormConfigFieldProviderInterface
{
    /**
     * Get the available fields for a content type.
     *
     * @param ContentTypeInterface $type
     *
     * @return FormConfigFieldInterface[]
     */
    public function getFields(ContentTypeInterface $type): array;
}
