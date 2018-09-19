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

class ChainProvider implements FormConfigFieldProviderInterface
{
    /**
     * @var FormConfigFieldProviderInterface[]
     */
    private $providers = [];

    /**
     * @param FormConfigFieldProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(ContentTypeInterface $type): array
    {
        if (!$this->providers) {
            return [];
        }

        $fields = [];

        foreach ($this->providers as $provider) {
            $fields[] = $provider->getFields($type);
        }

        return array_merge(...$fields);
    }
}
