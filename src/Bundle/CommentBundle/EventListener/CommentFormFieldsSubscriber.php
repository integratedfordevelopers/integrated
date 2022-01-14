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
use Integrated\Bundle\CommentBundle\Document\Comment;
use Integrated\Bundle\CommentBundle\Form\DataTransformer\CommentTagTransformer;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Content\Form\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class CommentFormFieldsSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var AssetManager
     */
    private $stylesheets;

    /**
     * @var AssetManager
     */
    private $javascripts;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array|null
     */
    private $comments = null;

    /**
     * @param DocumentManager $documentManager
     * @param AssetManager    $stylesheets
     * @param AssetManager    $javascripts
     * @param RequestStack    $requestStack
     */
    public function __construct(
        DocumentManager $documentManager,
        UrlGeneratorInterface $generator,
        AssetManager $stylesheets,
        AssetManager $javascripts,
        RequestStack $requestStack
    ) {
        $this->documentManager = $documentManager;
        $this->generator = $generator;
        $this->stylesheets = $stylesheets;
        $this->javascripts = $javascripts;
        $this->requestStack = $requestStack;
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
        $masterRequest = $this->requestStack->getMasterRequest();
        if (!$masterRequest instanceof Request
            || $masterRequest->attributes->get('_route') !== 'integrated_content_content_edit') {
            return;
        }

        /** @var Field $field */
        $field = $event->getField();

        $formOptions = $event->getOptions();
        /** @var Content $content */
        $content = $formOptions['data'];

        $options = $field->getOptions();

        if ($comment = $this->getComment($content->getId(), $field->getName())) {
            $options['attr'] = ['data-comment-id' => $comment[0]->getId()];

            $field->setOptions($options);
        }

        $urls = [
            'get' => $this->generator->generate('integrated_comment_get', ['comment' => '__comment__']),
            'new' => $this->generator->generate('integrated_comment_new', ['content' => '__content__', 'field' => '__field__']),
        ];

        $this->javascripts->add('var integrated_comment_urls = '.json_encode($urls), true);
        $this->javascripts->add('bundles/integratedcomment/js/comments.js');
    }

    /**
     * @param BuilderEvent $event
     */
    public function postBuildField(BuilderEvent $event)
    {
        if (!$event->getBuilder()->has($event->getField())) {
            return;
        }

        $field = $event->getBuilder()->get($event->getField());

        if ('integrated_editor' == $field->getType()->getBlockPrefix()) {
            $field->addViewTransformer(new CommentTagTransformer());
        }
    }

    /**
     * @param string $contentId
     *
     * @return array|mixed
     */
    protected function getComments($contentId)
    {
        if (null === $this->comments) {
            $comments = $this->documentManager->getRepository(Comment::class)
                ->findBy(['content.$id' => $contentId], ['date' => 'asc']);

            $this->comments = [];

            foreach ($comments as $comment) {
                $this->comments[$comment->getField()][] = $comment;
            }
        }

        return $this->comments;
    }

    /**
     * @param string $contentId
     * @param string $fieldName
     *
     * @return Comment[]|null
     */
    protected function getComment($contentId, $fieldName)
    {
        $comments = $this->getComments($contentId);

        return isset($comments[$fieldName]) ? $comments[$fieldName] : null;
    }
}
