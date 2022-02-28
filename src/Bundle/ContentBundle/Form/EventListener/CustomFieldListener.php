<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\EventListener;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField;
use Integrated\Bundle\ContentBundle\Form\Type\CustomFieldsType;
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldListener implements EventSubscriberInterface
{
    public const FORM_NAME = 'customFields';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_BUILD => 'onPostBuild',
        ];
    }

    /**
     * @param BuilderEvent $event
     */
    public function onPostBuild(BuilderEvent $event)
    {
        $type = $event->getContentType();

        foreach ($type->getFields() as $field) {
            if ($field instanceof CustomField) {
                $event->getBuilder()->add(self::FORM_NAME, CustomFieldsType::class, [
                    'contentType' => $type,
                ]);

                return;
            }
        }
    }
}
