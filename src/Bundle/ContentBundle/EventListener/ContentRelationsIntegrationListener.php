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

use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentRelationsIntegrationListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $name the field name
     * @param string $type the field type
     */
    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_BUILD => ['buildForm', 90],
        ];
    }

    public function buildForm(BuilderEvent $event)
    {
        $event->getBuilder()->add($this->name, $this->type, ['content_type' => $event->getContentType()]);
    }
}
