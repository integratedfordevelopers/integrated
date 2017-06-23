<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Integrated\Bundle\ContentBundle\Bulk\Action\ActionInterface;
use Integrated\Bundle\ContentBundle\Bulk\Registry\ActionHandlerRegistry;
use Integrated\Common\Content\ContentInterface;
use Traversable;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkHandler implements BulkHandlerInterface
{
    /**
     * @var ActionHandlerRegistry
     */
    private $registry;

    /**
     * @param ActionHandlerRegistry $registry
     */
    public function __construct(ActionHandlerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritdoc
     */
    public function execute($contents, $actions)
    {
        if (!$this->isTraversable($contents) || !$this->isTraversable($actions)) {
            new \RuntimeException('Both arguments need to be traversable for ' . __METHOD__ . ' in ' . __CLASS__);
        }

        if (!$contents = $this->filter($contents, ContentInterface::class)) {
            new \RuntimeException('No content of ' . ContentInterface::class . ' was found');
        }

        if (!$actions = $this->filter($actions, ActionInterface::class)) {
            new \RuntimeException('No action of ' . ActionInterface::class . ' was found');
        }

        foreach ($actions as $action) {
            if (!$this->registry->hasHandler($action->getName())) {
                new \RuntimeException('ActionHandler does not exist.');
            }
        }

        foreach ($actions as $action) {
            $handler = $this->registry->getHandler($action->getName());
            foreach ($contents as $content) {
                $handler->execute($content, $action->getOptions());
            }
        }
    }

    /**
     * @param $items
     * @return bool
     */
    private function isTraversable($items)
    {
        return is_array($items) || $items instanceof Traversable;
    }

    /**
     * @param Traversable|array $items
     * @param $class
     * @return array
     */
    private function filter($items, $class)
    {
        if ($items instanceof Traversable) {
            $items = iterator_to_array($items);
        }

        if (is_array($items)) {
            return array_filter($items, function ($var) use ($class) {
                return $var instanceof $class;
            });
        }

        return [];
    }
}
