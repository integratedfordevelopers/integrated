<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type\RelationChoice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RelationsType extends AbstractType
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $repo;

    /**
     * @var ArrayCollection
     */
    protected $relations;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->repo = $dm->getRepository('IntegratedContentBundle:Relation\Relation');
        $this->relations = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $relations = $event->getData();
            $form = $event->getForm();

            if (!$relations instanceof ArrayCollection) {
                return;
            }

            $options = $event->getForm()->getConfig()->getOptions();

            $relations = $this->ensureRelations($relations, $options);
            $this->addFormFields($form, $relations, $options);

            $event->setData($relations);
        });
    }


    /**
     * @param $relationId
     * @param Relation $relation
     */
    public function setRelation($relationId, Relation $relation)
    {
        $this->relations->set($relationId, $relation);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'relations' => [],
            'options' => []
        ]);
    }

    /**
     * @param ArrayCollection $relations
     * @param $options
     * @return ArrayCollection
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    protected function ensureRelations(ArrayCollection $relations, $options)
    {
        //get all relation ids
        $relationIds = [];
        foreach ($relations as $relation) {
            $relationIds[] = $relation->getRelationId();
        }

        foreach ((array) $options['relations'] as $relationId) {
            $relation = $this->repo->find($relationId);
            if (!$relation instanceof Relation) {
                continue;
            }

            $this->setRelation($relationId, $relation);

            //no need to add if the relation is already in the collection
            if (in_array($relationId, $relationIds)) {
                continue;
            }

            $embeddedRelation = new EmbeddedRelation();
            $embeddedRelation->setRelationId($relationId);
            $embeddedRelation->setRelationType($relation->getType());

            $relations->add($embeddedRelation);
        }

        return $relations;
    }

    /**
     * @param FormInterface $form
     * @param ArrayCollection $relations
     * @param $options
     */
    protected function addFormFields(FormInterface $form, ArrayCollection $relations, $options)
    {
        /** @var EmbeddedRelation $embeddedRelation */
        foreach ($relations as $key => $embeddedRelation) {
            if (!in_array($embeddedRelation->getRelationId(), $options['relations'])) {
                continue;
            }

            $relation = $this->getRelation($embeddedRelation->getRelationId());
            $contentTypes = [];
            foreach ($relation->getTargets() as $target) {
                $contentTypes[] = $target->getId();
            }

            $relationOptions = isset($options['options'][$embeddedRelation->getRelationId()]) ? $options['options'][$embeddedRelation->getRelationId()] : [];
            $relationOptions['contentTypes'] = $contentTypes;
            $relationOptions['multiple'] = $relation->isMultiple();
            if (!isset($relationOptions['label'])) {
                $relationOptions['label'] = $relation->getName();
            }

            $form->add($key, 'integrated_relation_choice', $relationOptions);
        }
    }

    /**
     * @param $id
     * @return Relation $relation
     */
    public function getRelation($id)
    {
        return $this->relations->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_relations_choice';
    }

}
