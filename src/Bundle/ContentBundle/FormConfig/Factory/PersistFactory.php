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

use Doctrine\Common\Persistence\ManagerRegistry;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;

class PersistFactory implements FormConfigFactoryInterface
{
    /**
     * @var FormConfigFactoryInterface
     */
    private $factory;

    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param FormConfigFactoryInterface $factory
     * @param ManagerRegistry            $registry
     */
    public function __construct(FormConfigFactoryInterface $factory, ManagerRegistry $registry)
    {
        $this->factory = $factory;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ContentTypeInterface $type, string $name): FormConfigEditableInterface
    {
        $config = $this->factory->create($type, $name);

        if ($manager = $this->registry->getManagerForClass(get_class($config))) {
            $manager->persist($config);
        }

        return $config;
    }
}
