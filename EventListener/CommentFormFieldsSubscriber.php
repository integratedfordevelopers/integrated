<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\CommentBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\CommentBundle\Form\DataTransformer\CommentTagTransformer;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Content\Form\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ContentCommentSubscriber
 */
class CommentFormFieldsSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var AssetManager
     */
    private $stylesheets;

    /**
     * @var AssetManager
     */
    private $javascripts;

    /**
     * @param DocumentManager $documentManager
     * @param AssetManager $stylesheets
     * @param AssetManager $javascripts
     */
    public function __construct(DocumentManager $documentManager, AssetManager $stylesheets, AssetManager $javascripts)
    {
        $this->documentManager = $documentManager;
        $this->stylesheets = $stylesheets;
        $this->javascripts = $javascripts;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::BUILD_FIELD => 'onBuildField',
            Events::POST_BUILD_FIELD => 'postBuildField',
        ];
    }

    /**
     * @param FieldEvent $event
     */
    public function onBuildField(FieldEvent $event)
    {
        /** @var Field $field */
        $field = $event->getField();

        $formOptions = $event->getOptions();
        /** @var Content $content */
        $content = $formOptions['data'];

        $options = $field->getOptions();

        $repository = $this->documentManager->getRepository('IntegratedCommentBundle:Comment');
        //todo one query per request
        if ($comment = $repository->findBy(['content.$id' => $content->getId(), 'field' => $field->getName()], ['date' => 'asc'])) {
            $comment = $comment[0];
            $options['attr'] = array('data-comment-id' => $comment->getId());
            $field->setOptions($options);
        }

        $this->stylesheets->add('bundles/integratedcomment/css/comments.css');
        $this->javascripts->add('bundles/integratedcomment/js/comments.js');
    }

    /**
     * @param FieldEvent $event
     */
    public function postBuildField(BuilderEvent $event)
    {
        $field = $event->getBuilder()->get($event->getField());

        if ($field->getType()->getName() == 'integrated_editor') {
            $field->addViewTransformer(new CommentTagTransformer());
        }
    }
}
