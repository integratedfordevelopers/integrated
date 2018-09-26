<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig\Manager;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Exception\NotFoundException;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigInterface;
use Integrated\Common\FormConfig\FormConfigManagerInterface;
use Iterator;

class DefaultAwareManager implements FormConfigManagerInterface
{
    /**
     * @var FormConfigManagerInterface
     */
    private $manager;

    /**
     * @var FormConfigFactoryInterface
     */
    private $factory;

    /**
     * @param FormConfigManagerInterface $manager
     * @param FormConfigFactoryInterface $factory
     */
    public function __construct(FormConfigManagerInterface $manager, FormConfigFactoryInterface $factory)
    {
        $this->manager = $manager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ContentTypeInterface $type, string $key): FormConfigInterface
    {
        try {
            return $this->manager->get($type, $key);
        } catch (NotFoundException $e) {
            if ($key !== 'default') {
                throw $e;
            }
        }

        return $this->factory->create($type, 'default');
    }

    /**
     * {@inheritdoc}
     */
    public function has(ContentTypeInterface $type, string $key): bool
    {
        if ($key === 'default') {
            return true;
        }

        return $this->manager->has($type, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function all(ContentTypeInterface $type = null): Iterator
    {
        $iterator = $this->manager->all($type);

        if ($type !== null) {
            $iterator = new DefaultAwareIterator(
                $type,
                $iterator,
                $this->factory
            );
        }

        return $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(FormConfigInterface $config): void
    {
        // No check is made to disallow removing the default configuration as when that happens
        // this manager will just create a new default instance if required. On top of that if
        // a content type is deleted then that would also be a valid reason to delete the default
        // configuration.

        $this->manager->remove($config);
    }

    /**
     * {@inheritdoc}
     */
    public function save(FormConfigInterface $config): void
    {
        $this->manager->save($config);
    }
}
