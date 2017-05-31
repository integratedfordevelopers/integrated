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

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkHandler implements BulkHandlerInterface
{
    /**
     * @param Collection $contents [ ContentInterface ]
     * @param Collection $actions [ ActionInterface ]
     * @return $this
     */
    public function execute(Collection $contents, Collection $actions)
    {
        // TODO make sure this works
//        if ($bulkAction->getState() !== BuildState::CONFIRMED) {
//            throw new \RuntimeException("Its seems not all steps have been completed.");
//        }
//
//        foreach ($bulkAction->getSelection() as $content) {
//            foreach ($bulkAction->getActions() as $action) {
//                if ($action instanceof ActionInterface) {
//                    $action->execute($content);
//                }
//            }
//        }
//
//        $bulkAction->setState(BuildState::EXECUTED);
//        $bulkAction->setExecutedAt(new \DateTime());

        return $this;
    }
}