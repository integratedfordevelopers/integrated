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
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldListener implements EventSubscriberInterface
{
    const FORM_NAME = 'customFields';
    const FORM_TYPE = 'integrated_custom_fields';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_BUILD => 'onPostBuild'
        ];
    }

    /**
     * @param BuilderEvent $event
     */
    public function onPostBuild(BuilderEvent $event)
    {
        $contentType = $event->getContentType();
        $builder = $event->getBuilder();

        foreach ($contentType->getFields() as $field) {
            if ($field instanceof CustomField) {
                $builder->add(
                    self::FORM_NAME,
                    self::FORM_TYPE,
                    [
                        'contentType' => $contentType
                    ]
                );

                break;
            }
        }
    }
}
