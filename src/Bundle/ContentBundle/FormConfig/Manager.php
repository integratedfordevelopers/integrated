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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\FormConfig\FormConfig;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Exception\NotFoundException;
use Integrated\Common\FormConfig\Exception\UnexpectedTypeException;
use Integrated\Common\FormConfig\FormConfigInterface;
use Integrated\Common\FormConfig\FormConfigManagerInterface;
use Iterator;

class Manager implements FormConfigManagerInterface
{
    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @param DocumentManager $manager
     */
    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ContentTypeInterface $type, string $key): FormConfigInterface
    {
        $config = $this->findConfig($type->getId(), $key);

        if (!$config) {
            throw new NotFoundException($type->getId(), $key);
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function has(ContentTypeInterface $type, string $key): bool
    {
        return $this->findConfig($type->getId(), $key) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function all(ContentTypeInterface $type = null): Iterator
    {
        $builder = $this->manager->createQueryBuilder(FormConfig::class);

        if ($type) {
            $builder->field('_id.type')->equals($type->getId());
        }

        return $builder->getQuery()->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(FormConfigInterface $config): void
    {
        if (!$config instanceof FormConfig) {
            throw new UnexpectedTypeException($config, FormConfig::class);
        }

        $this->manager->remove($config);
        $this->manager->flush($config);
    }

    /**
     * {@inheritdoc}
     */
    public function save(FormConfigInterface $config): void
    {
        if (!$config instanceof FormConfig) {
            throw new UnexpectedTypeException($config, FormConfig::class);
        }

        $this->manager->flush($config);
    }

    /**
     * @param string $type
     * @param string $key
     *
     * @return FormConfig
     */
    private function findConfig(string $type, string $key):? FormConfig
    {
        $config = $this->manager->find(FormConfig::class, [
            'type' => $type,
            'key' => $key
        ]);

        return $config;
    }
}
