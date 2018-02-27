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

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\Bulk\BulkActionInterface;
use Integrated\Common\Bulk\Form\ActionMatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RelationFormActionMatcher implements ActionMatcherInterface
{
    /**
     * @var string
     */
    private $handler;

    /**
     * @var string
     */
    private $relation;

    /**
     * @param string $handler
     * @param string $relation
     */
    public function __construct($handler, $relation)
    {
        $this->handler = (string) $handler;
        $this->relation = $relation;
    }

    /**
     * {@inheritdoc}
     */
    public function match(BulkActionInterface $action)
    {
        if ($action->getHandler() !== $this->handler) {
            return false;
        }

        $options = $action->getOptions();

        if (isset($options['relation'])) {
            if ($options['relation'] instanceof Relation && $options['relation']->getId() == $this->relation) {
                return true;
            }

            if ($options['relation'] === $this->relation) {
                return true;
            }
        }

        return false;
    }
}
