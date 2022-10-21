<?php

namespace Integrated\Bundle\FormTypeBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Relation\HtmlRelation;
use Integrated\Bundle\FormTypeBundle\Form\Type\EditorType;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EditorImageRelationEventListener implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @var HtmlRelation
     */
    private $parser;

    public function __construct(DocumentManager $manager, HtmlRelation $parser)
    {
        $this->manager = $manager;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'handleRelations',
        ];
    }

    public function handleRelations(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$form->isRoot()) {
            return;
        }

        $content = $event->getData();

        if (!$content instanceof ContentInterface) {
            return;
        }

        $relation = $content->getRelation(EditorType::RELATION);

        if (!$relation = $content->getRelation(EditorType::RELATION)) {
            $relation = (new Relation())
                ->setRelationId(EditorType::RELATION)
                ->setRelationType('embedded');
        }

        $relation->getReferences()->clear();

        foreach ($event->getForm() as $child) {
            if ($child->getConfig()->getType()->getInnerType() instanceof EditorType) {
                $data = $child->getData();

                if (\is_string($data) && trim($data)) {
                    foreach ($this->parser->read($data) as $id) {
                        if ($image = $this->manager->find(File::class, $id)) {
                            $relation->addReference($image);
                        }
                    }
                }
            }
        }

        if ($relation->getReferences()->count()) {
            $content->addRelation($relation);
        } else {
            $content->removeRelation($relation);
        }
    }
}
