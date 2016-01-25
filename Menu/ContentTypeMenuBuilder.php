<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Menu;

use Integrated\Common\ContentType\ContentTypeFilterInterface;
use Knp\Menu\FactoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeMenuBuilder
{
    const CONTENT_CLASS = 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Content';
    const CONTENT_TYPE_CLASS = 'Integrated\\Bundle\\ContentBundle\\Document\\ContentType\\ContentType';
    const ROUTE = 'integrated_content_content_new';

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var ContentTypeFilterInterface
     */
    protected $workflowPermission;

    /**
     * @param FactoryInterface           $factory
     * @param ObjectRepository           $repository
     * @param ContentTypeFilterInterface $workflowPermission
     */
    public function __construct(FactoryInterface $factory, ObjectRepository $repository, ContentTypeFilterInterface $workflowPermission = null)
    {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->workflowPermission = $workflowPermission;
    }

    /**
     * @return \Knp\Menu\ItemInterface|void
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');

        $result = $this->repository->findBy([], ['name' => 'ASC']);

        foreach ($this->group($result) as $key => $documents) {
            $child = $menu->addChild($key);

            /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\ContentType $document */
            foreach ($documents as $document) {

                if ($this->workflowPermission !== null && !$this->workflowPermission->hasAccess($document)) {
                    continue;
                }

                $child->addChild(
                    $document->getName(),
                    ['route' => self::ROUTE, 'routeParameters' => ['type' => $document->getType()]]
                );
            }
        }

        return $menu;
    }

    /**
     * @param array $result
     * @return array
     */
    protected function group(array $result)
    {
        $menu = [];

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\ContentType $document */
        foreach ($result as $document) {
            if (!is_a($document, self::CONTENT_TYPE_CLASS)) {
                continue;
            }

            $reflectionClass = new \ReflectionClass($document->getClass());

            if ($parent = $this->getParentClass($reflectionClass)) {
                $menu[$parent->getShortName()][] = $document;
            } else {
                $menu[$reflectionClass->getShortName()][] = $document;
            }
        }

        ksort($menu);

        return $menu;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return bool|\ReflectionClass
     */
    protected function getParentClass(\ReflectionClass $reflectionClass)
    {
        if ($parent = $reflectionClass->getParentClass()) {
            if ($parent->getName() === self::CONTENT_CLASS) {
                return $reflectionClass;
            }

            return $this->getParentClass($parent);
        }

        return false;
    }
}
