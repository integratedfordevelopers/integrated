<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Adaptor\Metadata;

use Integrated\Common\Content\Extension\Adaptor\AbstractAdaptor;
use Integrated\Common\Content\Extension\Events as ExtensionEvents;
use Integrated\Common\Form\Mapping\Event\MetadataEvent;
use Integrated\Common\Form\Mapping\Events as MetadataEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MetadataFactoryAdaptor extends AbstractAdaptor implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MetadataEvents::METADATA => 'dispatch',
        ];
    }

    public function dispatch(MetadataEvent $event)
    {
        if (($dispatcher = $this->getDispatcher()) === null) {
            return;
        }

        $this->dispatcher->dispatch(ExtensionEvents::METADATA, $event->getMetadata());
    }
}
