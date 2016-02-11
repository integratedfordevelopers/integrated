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
     * @var DecisionInterface
     */
    protected $decision;

    /**
     * @param ManagerInterface $manager
     * @param DecisionInterface $decision
     */
    public function __construct(ManagerInterface $manager, DecisionInterface $decision)
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
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        // The file property in the form
        $file = $event->getForm()->get('file')->getData();

        if ($event->getForm()->get('remove')->getData()) {
            $file->setData(null);
        } elseif ($file instanceof UploadedFile) {
            // Make sure the entity ends up a StorageInterface
            $event->setData($this->manager->write(
                new UploadedFileReader($event->getForm()->get('file')->getData()),
                // Set the file to allowed entity filesystems
                $this->decision->getFilesystems($event->getForm()->getData())
            ));
        } else {
            // We don't know what to do
            throw new \LogicException(
                'Invalid class given in submit event, expected %s got %s.',
                UploadedFile::class,
                get_class($file)
            );
        }
    }
}
