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

use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkExecutor
{
    /**
     * @param BulkAction $bulkAction
     * @return $this
     */
    public function execute(BulkAction $bulkAction)
    {
        if ($bulkAction->getState() !== BuildState::CONFIRMED) {
            throw new \RuntimeException("Its seems not all steps have been completed.");
        }

        foreach ($bulkAction->getSelection() as $content) {
            foreach ($bulkAction->getActions() as $action) {
                if ($action instanceof ActionInterface) {
                    $action->execute($content);
                }
            }
        }

        $bulkAction->setState(BuildState::EXECUTED);
        $bulkAction->setExecutedAt(new \DateTime());

        return $this;
    }
}