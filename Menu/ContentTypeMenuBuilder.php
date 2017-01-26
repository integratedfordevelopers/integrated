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

use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Common\ContentType\ContentTypeFilterInterface;

use Knp\Menu\FactoryInterface;

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
     * @var ContentTypeManager
     */
    protected $contentTypeManager;

    /**
     * @var ContentTypeFilterInterface
     */
    protected $workflowPermission;

    /**
     * @param FactoryInterface           $factory
     * @param ContentTypeManager         $contentTypeManager
     * @param ContentTypeFilterInterface $workflowPermission
     */
    public function __construct(
        FactoryInterface $factory,
        ContentTypeManager $contentTypeManager,
        ContentTypeFilterInterface $workflowPermission = null
    ) {
        $this->factory = $factory;
        $this->contentTypeManager = $contentTypeManager;
        $this->workflowPermission = $workflowPermission;
    }

    /**
     * @return \Knp\Menu\ItemInterface|void
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');

        $contentTypes = $this->contentTypeManager->getAll();

        foreach ($this->group($contentTypes) as $key => $documents) {
            $child = $menu->addChild($key);

            /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\ContentType $document */
            foreach ($documents as $document) {
                if ($this->workflowPermission !== null && !$this->workflowPermission->hasAccess($document)) {
                    continue;
                }

                $child->addChild(
                    $document->getName(),
                    ['route' => self::ROUTE, 'routeParameters' => ['type' => $document->getId()]]
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
