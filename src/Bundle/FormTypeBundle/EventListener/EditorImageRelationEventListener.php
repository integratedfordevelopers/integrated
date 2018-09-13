<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Event\HandleRequestEvent;
use Integrated\Bundle\ContentBundle\Events\IntegratedHttpRequestHandlerEvents;
use Integrated\Bundle\ContentBundle\Relation\HtmlRelation;
use Integrated\Bundle\FormTypeBundle\Form\Type\EditorType;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class EditorImageRelationEventListener implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var HtmlRelation
     */
    private $htmlRelation;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->htmlRelation = new HtmlRelation();
        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            IntegratedHttpRequestHandlerEvents::POST_HANDLE => 'postHandleRequest',
        ];
    }

    /**
     * @param HandleRequestEvent $event
     */
    public function postHandleRequest(HandleRequestEvent $event)
    {
        // The document binded to the form
        $document = $event->getData();

        if ($document instanceof ContentInterface) {
            // Check if we've already got something
            if ($relation = $document->getRelation(EditorType::RELATION)) {
                $relation->getReferences()->clear();
            } else {
                $relation = (new Relation())
                    ->setRelationId(EditorType::RELATION)
                    ->setRelationType('embedded')
                ;
            }

            // Add the new relations
            foreach ($event->getForm()->all() as $form) {
                $type = $form->getConfig()->getType()->getInnerType();
                if ($type instanceof EditorType) {
                    if ($data = $form->getData()) {
                        foreach ($this->htmlRelation->read($data) as $id) {
                            if ($image = $this->documentManager->find(File::class, $id)) {
                                $relation->addReference($image);
                            }
                        }
                        foreach ($this->htmlRelation->read($data, 'video') as $id) {
                            if ($image = $this->documentManager->find(File::class, $id)) {
                                $relation->addReference($image);
                            }
                        }
                    }
                }
            }

            // Only add the relation if there's something
            if ($relation->getReferences()->count()) {
                $document->addRelation($relation);
            }
        }
    }
}
