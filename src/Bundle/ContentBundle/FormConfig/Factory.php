<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Identifier;
use Integrated\Bundle\ContentBundle\Document\FormConfig\FormConfig;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;

class Factory implements FormConfigFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return FormConfig
     */
    public function create(ContentTypeInterface $type, string $key): FormConfigEditableInterface
    {
        return new FormConfig(new Identifier($type->getId(), $key));
    }
}
