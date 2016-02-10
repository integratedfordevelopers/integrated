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
            FormEvents::POST_SUBMIT => 'postSubmit'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (!empty($data['remove'])) {
            // Remove the file
            $event->setData(['file' => null]);
        }
    }
}
