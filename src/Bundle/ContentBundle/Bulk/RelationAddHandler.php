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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;
use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Relation\RelationInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RelationAddHandler implements HandlerInterface
{
    /**
     * @var RelationInterface
     */
    private $relation;

    /**
     * @var string[]
     */
    private $relationTypes = [];

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

        foreach ($this->relation->getSources() as $source) {
            $this->relationTypes[$source->getId()] = $source->getId();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        if (!\array_key_exists($content->getContentType(), $this->relationTypes)) {
            // content does not have a relation of the type this handler should add.
            return;
        }

        $embedded = $content->getRelation($this->relation->getId());

        if (!$embedded) {
            $embedded = new Relation();
            $embedded->setRelationId($this->relation->getId());
            $embedded->setRelationType($this->relation->getType());

            $content->addRelation($embedded);
        }

        foreach ($this->references as $reference) {
            $embedded->addReference($reference);
        }
    }
}
