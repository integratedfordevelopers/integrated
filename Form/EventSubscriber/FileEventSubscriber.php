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

use Integrated\Bundle\StorageBundle\Form\Upload\StorageIntentUpload;
use Integrated\Bundle\StorageBundle\Storage\Reader\UploadedFileReader;

use Integrated\Common\Storage\DecisionInterface;
use Integrated\Common\Storage\ManagerInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FileEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'submit'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        // The file property in the form
        $file = $event->getForm()->get('file')->getData();

        // Delete comes first
        if ($event->getForm()->get('remove')->getData()) {
            // Delete the set the property to null
            $event->setData(null);
        } elseif ($file instanceof UploadedFile) {
            // Get the root document bind to the form
            $rootForm = $event->getForm();
            while ($rootForm->getParent()) {
                $rootForm = $rootForm->getParent();
            }

            // Make sure the entity ends up a StorageInterface
            $event->setData(
                 new StorageIntentUpload($event->getForm()->getData(), $file)
            );
        } else {
            // Set the something we don't know
            $event->setData($file);
        }
    }
}
