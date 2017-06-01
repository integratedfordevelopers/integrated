<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\BulkHandler;

use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\ContentBundle\Bulk\ActionInterface;
use Integrated\Bundle\ContentBundle\Bulk\Registry\ActionHandlerRegistry;

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
     * @param Collection $contents
     * @param Collection $actions
     * @return $this
     */
    public function execute(Collection $contents, Collection $actions)
    {
        /* @var ActionInterface $action */
        foreach ($actions->getIterator() as $action) {
            if (!$this->registry->hasHandler($action->getName())) {
                new \RuntimeException('ActionHandler does not exist.');
            }
        }

        /* @var ActionInterface $action */
        foreach ($actions->getIterator() as $action) {
            $handler = $this->registry->getHandler($action->getName());
            foreach ($contents->getIterator() as $content){
                $handler->execute($content, $action->getOptions());
            }
        }

        return $this;
    }
}