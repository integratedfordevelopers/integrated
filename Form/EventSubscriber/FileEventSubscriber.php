<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\EventSubscriber;

use Integrated\Bundle\StorageBundle\Storage\Decision;
use Integrated\Bundle\StorageBundle\Storage\Reader\UploadedFileReader;
use Integrated\Common\Storage\ManagerInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var Decision
     */
    protected $decision;

    /**
     * @param ManagerInterface $manager
     * @param Decision $decision
     */
    public function __construct(ManagerInterface $manager, Decision $decision)
    {
        $this->manager = $manager;
        $this->decision = $decision;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmitData'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmitData(FormEvent $event)
    {
        if ($event->getData()) {
            // Write and set the data in the entity
            $event->setData(
                $this->manager->write(
                    new UploadedFileReader($event->getData()),
                    $this->decision->getFilesystems($event->getForm()->getData())
                )
            );
        }
    }
}
