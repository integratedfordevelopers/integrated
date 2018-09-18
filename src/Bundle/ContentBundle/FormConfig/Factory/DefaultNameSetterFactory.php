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

class DefaultNameSetterFactory implements FormConfigFactoryInterface
{
    /**
     * @var FormConfigFactoryInterface
     */
    private $factory;

    /**
     * @var string
     */
    private $name;

    /**
     * @param FormConfigFactoryInterface $factory
     * @param string                     $name
     */
    public function __construct(FormConfigFactoryInterface $factory, string $name)
    {
        $this->factory = $factory;
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function create(ContentTypeInterface $type, string $key): FormConfigEditableInterface
    {
        $config = $this->factory->create($type, $key);
        $config->setName($this->name);

        return $config;
    }
}
