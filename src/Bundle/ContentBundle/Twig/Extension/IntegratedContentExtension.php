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
        ];
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function integratedContent($content)
    {
        $contentEvent = new ContentEvent($content);
        $this->eventDispatcher->dispatch($contentEvent, ContentEvent::NAME);

        return $contentEvent->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_integrated_content_extension';
    }
}
