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

use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Relation\RelationInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RelationRemoveHandler implements HandlerInterface
{
    /**
     * @var RelationInterface
     */
    private $relation;

    /**
     * @var ContentInterface[]
     */
    private $references;

    /**
     * Constructor.
     *
     * @param RelationInterface  $relation
     * @param ContentInterface[] $references
     */
    public function __construct(RelationInterface $relation, $references)
    {
        $this->relation = $relation;
        $this->references = $references;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        $embedded = $content->getRelation($this->relation->getId());

        if (!$embedded) {
            return;
        }

        foreach ($this->references as $reference) {
            $embedded->removeReference($reference);
        }

        if (!\count($embedded->getReferences())) {
            $content->removeRelation($embedded);
        }
    }
}
