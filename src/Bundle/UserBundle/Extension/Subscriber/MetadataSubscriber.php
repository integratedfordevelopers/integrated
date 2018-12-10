<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Extension\Subscriber;

use Integrated\Bundle\UserBundle\Form\Type\UserFormType;
use Integrated\Common\Content\Extension\Event\MetadataEvent;
use Integrated\Common\Content\Extension\Event\Subscriber\MetadataSubscriberInterface;
use Integrated\Common\Content\Extension\Events;
use Integrated\Common\Content\Extension\ExtensionInterface;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MetadataSubscriber implements MetadataSubscriberInterface
{
    const RELATION_CLASS = 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Relation\\Relation';

    /**
     * @var ExtensionInterface
     */
    private $extension;

    /**
     * @param ExtensionInterface $extension
     */
    public function __construct(ExtensionInterface $extension)
    {
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::METADATA => 'process',
        ];
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function process(MetadataEvent $event)
    {
        $metadata = $event->getMetadata();

        if ($metadata->getClass() === self::RELATION_CLASS || is_subclass_of($metadata->getClass(), self::RELATION_CLASS)) {
            $field = $metadata->newField('User');

            $field->setType(UserFormType::class);

            $field->setOption('mapped', false);
            $field->setOption('optional', true);
            $field->setOption('constraints', [new Valid([])]);

            $metadata->addField($field);
        }
    }
}
