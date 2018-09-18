<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\FormConfig\Field;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class CacheProvider implements FormConfigFieldProviderInterface
{
    /**
     * @var FormConfigFieldProviderInterface
     */
    private $provider;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param FormConfigFieldProviderInterface $provider
     */
    public function __construct(FormConfigFieldProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function getFields(ContentTypeInterface $type): array
    {
        if (!array_key_exists($type->getId(), $this->cache)) {
            $this->cache[$type->getId()] = $this->provider->getFields($type);
        }

        return $this->cache[$type->getId()];
    }
}
