<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentBundle\Bulk\Action\ActionInterface;
use Integrated\Bundle\ContentBundle\Bulk\Action\Handler\AddReferenceHandler;
use Integrated\Bundle\ContentBundle\Bulk\Action\Handler\RemoveReferenceHandler;
use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;
use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Event\BulkActionFormEvent;
use Integrated\Bundle\ContentBundle\Events\BulkActionFormEvents;
use Integrated\Bundle\ContentBundle\Form\Type\BulkRelationActionType;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class BulkActionAddActionsListener implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BulkActionFormEvents::ADD_ACTIONS => 'addActions'
        ];
    }

    /**
     * @param BulkActionFormEvent $event
     */
    public function addActions(BulkActionFormEvent $event)
    {
        $types = [];
        foreach ($event->getBulkAction()->getSelection() as $content) {
            $types[$content->getContentType()] = $content->getContentType();
        }

        $qb = $this->dm->getRepository(Relation::class)->createQueryBuilder();
        $qb->field('sources.$id')->in($types);

        if ($relations = $qb->getQuery()->execute()) {
            /** @var Relation $relation */
            foreach ($relations as $relation) {
                $event->getForm()->add(
                    sprintf('add_%s', $relation->getId()),
                    BulkRelationActionType::class,
                    [
                        'relation' => $relation,
                        'handler' => AddReferenceHandler::class,
                        'label' => sprintf('Add %s', $relation->getName()),
                        'data' => $this->getAction(
                            $event->getBulkAction(),
                            $relation,
                            AddReferenceHandler::class
                        )
                    ]
                );

                $event->getForm()->add(
                    sprintf('delete_%s', $relation->getId()),
                    BulkRelationActionType::class,
                    [
                        'relation' => $relation,
                        'handler' => RemoveReferenceHandler::class,
                        'label' => sprintf('Remove %s', $relation->getName()),
                        'data' => $this->getAction(
                            $event->getBulkAction(),
                            $relation,
                            RemoveReferenceHandler::class
                        )
                    ]
                );
            }
        }
    }

    /**
     * @param BulkAction $bulkAction
     * @param Relation $relation
     * @param string $handler
     * @return ActionInterface|null
     */
    protected function getAction(BulkAction $bulkAction, Relation $relation, $handler)
    {
        foreach ($bulkAction->getActions() as $action) {
            if (!$action instanceof RelationAction) {
                continue;
            }

            if ($action->getName() === $handler && $action->getRelation()->getId() === $relation->getId()) {
                return $action;
            }
        }

        return null;
    }
}
