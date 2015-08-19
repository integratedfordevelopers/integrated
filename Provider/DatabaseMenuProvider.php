<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class DatabaseMenuProvider implements MenuProviderInterface
{
    /**
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * @var ItemInterface[]
     */
    protected $menus = [];

    /**
     * @param DocumentRepository $repository
     */
    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = [])
    {
        if (!isset($this->menus[$name])) {
            $this->menus[$name] = $this->repository->find($name);
        }

        return $this->menus[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = [])
    {
        return null !== $this->get($name, $options);
    }
}
