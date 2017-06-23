<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Twig\Extension;

use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Comment;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Bundle\ContentBundle\Event\ContentEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Michael Jongman <michael@e-active.nl>
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class IntegratedContentExtension extends \Twig_Extension
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('integrated_content', [$this, 'integratedContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('content_name', [$this, 'contentName'])
        ];
    }

    /**
     * @param string $content
     * @return string
     */
    public function integratedContent($content)
    {
        $contentEvent = new ContentEvent($content);
        $this->eventDispatcher->dispatch(ContentEvent::NAME, $contentEvent);

        return $contentEvent->getContent();
    }

    /**
     * This filter return the correct name for different classes which extend Content
     * @param $content
     * @return string
     */
    public function contentName($content)
    {
        if (!$content instanceof Content) {
            return $content;
        }

        switch (true) {
            case $content instanceof Comment:
            case $content instanceof Article:
            case $content instanceof File:
            case $content instanceof Taxonomy:
                return $content->getTitle();
            case $content instanceof Company:
                return $content->getName();
            case $content instanceof Person:
                return $content->getFirstName() . " " . $content->getLastName();
            default:
                return $content->getId();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_integrated_content_extension';
    }
}
