<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\Bulk;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentBundle\Bulk\ActionHandler\AddReferenceActionHandler;
use Integrated\Bundle\ContentBundle\Bulk\ActionHandler\RemoveReferenceActionHandler;
use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ActionsTransformer;
use Integrated\Bundle\ContentBundle\Form\Type\Bulk\Action\RelationActionType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionsType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $model = $form->getParent()->getData();

            if (!$model instanceof BulkAction) {
                return;
            }

            $types = [];
            foreach ($model->getSelection() as $content) {
                $types[$content->getContentType()] = $content->getContentType();
            }

            $qb = $this->dm->getRepository(Relation::class)->createQueryBuilder();
            $qb->field('sources.$id')->in($types);

            $getAction = function ($handler, Relation $relation) use ($model) {
                foreach ($model->getActions() as $action) {
                    if (!$action instanceof RelationAction) {
                        continue;
                    }

                    if ($action->getName() === $handler && $action->getRelation()->getId() === $relation->getId()) {
                        return $action;
                    }
                }

                return null;
            };

            if ($relations = $qb->getQuery()->execute()) {
                foreach ($relations as $relation) {
                    $form->add(
                        sprintf('add_%s', $relation->getId()),
                        RelationActionType::class,
                        [
                            'relation' => $relation,
                            'handler' => AddReferenceActionHandler::class,
                            'label' => sprintf('Add %s', $relation->getName()),
                            'data' => $getAction(AddReferenceActionHandler::class, $relation),
                        ]
                    );

                    $form->add(
                        sprintf('delete_%s', $relation->getId()),
                        RelationActionType::class,
                        [
                            'relation' => $relation,
                            'handler' => RemoveReferenceActionHandler::class,
                            'label' => sprintf('Remove %s', $relation->getName()),
                            'data' => $getAction(RemoveReferenceActionHandler::class, $relation),
                        ]
                    );
                }
            }
        });

        $builder->addModelTransformer(new ActionsTransformer());
    }
}
