<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk;

use Integrated\Common\Bulk\Action\HandlerFactoryRegistry;
use Integrated\Common\Bulk\Exception\UnexpectedTypeException;
use Integrated\Common\Content\ContentInterface;
use Traversable;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkHandler implements BulkHandlerInterface
{
    /**
     * @var HandlerFactoryRegistry
     */
    private $registry;

    /**
     * @param HandlerFactoryRegistry $registry
     */
    public function __construct(HandlerFactoryRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($content, $actions)
    {
        if (!\is_array($content) && !$content instanceof Traversable) {
            throw new UnexpectedTypeException($content, 'array or Traversable');
        }

        if (!\is_array($actions) && !$actions instanceof Traversable) {
            throw new UnexpectedTypeException($actions, 'array or Traversable');
        }

        foreach ($content as $item) {
            if (!$item instanceof ContentInterface) {
                throw new UnexpectedTypeException($item, ContentInterface::class);
            }
        }

        $handlers = [];

        foreach ($actions as $action) {
            if (!$action instanceof BulkActionInterface) {
                throw new UnexpectedTypeException($action, BulkActionInterface::class);
            }

            $handlers[] = $this->registry->getFactory($action->getHandler())->createHandler($action->getOptions());
        }

        foreach ($handlers as $handler) {
            foreach ($content as $item) {
                $handler->execute($item);
            }
        }
    }
}
