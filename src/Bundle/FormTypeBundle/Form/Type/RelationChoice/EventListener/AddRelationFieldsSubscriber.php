<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type\RelationChoice\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\FormTypeBundle\Form\Type\RelationChoice\RelationReferencesType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class AddRelationFieldsSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    /**
     * @var \Doctrine\ODM\MongoDB\Repository\DocumentRepository
     */
    protected $repo;

    /**
     * @var Collection
     */
    protected $relations;

    /**
     * @var Collection
     */
    protected $embeddedRelations;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param DocumentManager $dm
     * @param array           $options
     */
    public function __construct(DocumentManager $dm, array $options)
    {
        $this->repo = $dm->getRepository(Relation::class);
        $this->relations = new ArrayCollection();
        $this->options = $options;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $this->ensureRelations($event);
        $this->addFormFields($event);
    }

    /**
     * @param FormEvent $event
     *
     * @throws \Exception
     */
    protected function ensureRelations(FormEvent $event)
    {
        $relations = $event->getData();

        if (!$relations instanceof Collection) {
            throw new \Exception(sprintf('Relations should implement Collection, "%s" given', \gettype($relations)));
        }

        // get all relation ids
        $relationIds = [];
        foreach ($relations as $relation) {
            $relationIds[] = $relation->getRelationId();
        }

        foreach ($this->options['relations'] as $relationId) {
            $relation = $this->findRelation($relationId, $event->getForm()->getParent()->getData());

            // no need to add if the relation is already in the collection
            if (\in_array($relationId, $relationIds)) {
                continue;
            }

            $embeddedRelation = new EmbeddedRelation();
            $embeddedRelation->setRelationId($relationId);
            $embeddedRelation->setRelationType($relation->getType());

            $relations->add($embeddedRelation);
        }

        $this->setEmbeddedRelations($relations);
        $event->setData($relations);
    }

    /**
     * @param string $relationId
     * @param object $formData
     *
     * @return Relation|object
     *
     * @throws \Exception
     */
    protected function findRelation($relationId, $formData)
    {
        $relation = $this->repo->find($relationId);
        if (!$relation instanceof Relation) {
            throw new \Exception(sprintf('RelationId "%s" is not found', $relationId));
        }

        $relationSourceClasses = [];
        $sources = $relation->getSources();

        foreach ($sources as $source) {
            $relationSourceClasses[] = $source->getClass();
        }

        $formClass = \get_class($formData);

        if (!\in_array($formClass, $relationSourceClasses)) {
            throw new \Exception(sprintf('RelationId "%s" does not have "%s" defined as source, perhaps you have chosen a wrong relation?', $relationId, $formClass));
        }

        $this->setRelation($relationId, $relation);

        return $relation;
    }

    /**
     * @param FormEvent $event
     */
    protected function addFormFields(FormEvent $event)
    {
        /** @var EmbeddedRelation $embeddedRelation */
        foreach ($this->getEmbeddedRelations() as $key => $embeddedRelation) {
            if (!\in_array($embeddedRelation->getRelationId(), $this->options['relations'])) {
                continue;
            }

            $relation = $this->getRelation($embeddedRelation->getRelationId());
            $contentTypes = [];

            foreach ($relation->getTargets() as $target) {
                $contentTypes[] = $target->getId();
            }

            $relationOptions = isset($this->options['options'][$embeddedRelation->getRelationId()]) ?
                $this->options['options'][$embeddedRelation->getRelationId()] : [];

            $relationOptions['content_types'] = $contentTypes;
            $relationOptions['multiple'] = isset($relationOptions['multiple']) ?
                $relationOptions['multiple'] : $relation->isMultiple();

            if (!isset($relationOptions['label'])) {
                $relationOptions['label'] = $relation->getName();
            }

            $event->getForm()->add($key, RelationReferencesType::class, ['options' => $relationOptions]);
        }
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
     * @param $id
     *
     * @return Relation $relation
     */
    public function getRelation($id)
    {
        return $this->relations->get($id);
    }

    /**
     * @return Relation
     */
    public function getRelations()
    {
        return $this->relations->toArray();
    }

    /**
     * @param Collection $relations
     */
    public function setEmbeddedRelations(Collection $relations)
    {
        $this->embeddedRelations = $relations;
    }

    /**
     * @return array
     */
    public function getEmbeddedRelations()
    {
        return $this->embeddedRelations->toArray();
    }
}
