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
    public function get($id, array $options = [])
    {
        if (!isset($this->menus[$id])) {
            if ($menu = $this->repository->find($id)) {
                $this->menus[$id] = $menu;
            }
        }

        if (isset($this->menus[$id])) {
            return $this->menus[$id];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id, array $options = [])
    {
        return null !== $this->get($id, $options);
    }
}
