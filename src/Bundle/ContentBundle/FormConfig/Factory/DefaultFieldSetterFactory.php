<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig\Factory;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class DefaultFieldSetterFactory implements FormConfigFactoryInterface
{
    /**
     * @var FormConfigFactoryInterface
     */
    private $factory;

    /**
     * @var FormConfigFieldProviderInterface
     */
    private $provider;

    /**
     * @param FormConfigFactoryInterface       $factory
     * @param FormConfigFieldProviderInterface $provider
     */
    public function __construct(FormConfigFactoryInterface $factory, FormConfigFieldProviderInterface $provider)
    {
        $this->factory = $factory;
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ContentTypeInterface $type, string $name): FormConfigEditableInterface
    {
        $config = $this->factory->create($type, $name);
        $config->setFields($this->provider->getFields($type));

        return $config;
    }
}
