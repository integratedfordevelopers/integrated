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

use Integrated\Common\Bulk\BulkActionInterface;
use Integrated\Common\Bulk\Form\ActionMatcherInterface;

class DeleteFormActionMatcher implements ActionMatcherInterface
{
    /**
     * @var string
     */
    private $handler;

    /**
     * @param string $handler
     */
    public function __construct($handler)
    {
        $this->handler = (string) $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function match(BulkActionInterface $action)
    {
        if ($action->getHandler() !== $this->handler) {
            return false;
        }

        return true;
    }
}
