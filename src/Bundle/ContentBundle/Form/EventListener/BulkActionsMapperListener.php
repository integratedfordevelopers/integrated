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

use Integrated\Common\Bulk\BulkActionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class BulkActionsMapperListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $mappings;

    /**
     * @var bool
     */
    private $readonly;

    /**
     * @param array $mappings
     * @param bool  $readonly
     */
    public function __construct(array $mappings, $readonly)
    {
        $this->mappings = $mappings;
        $this->readonly = $readonly;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'distribute',
            FormEvents::SUBMIT => 'collect',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function distribute(FormEvent $event)
    {
        $data = $event->getData();

        if (!\is_array($data)) {
            return;
        }

        foreach ($data as $action) {
            if (!$action instanceof BulkActionInterface) {
                continue;
            }

            if (!isset($this->mappings[$action->getHandler()])) {
                continue;
            }

            foreach ($this->mappings[$action->getHandler()] as $mapping) {
                if ($mapping['matcher']->match($action) && $parent = $event->getForm()->get($mapping['name'])) {
                    $parent->get('active')->setData(true);
                    $parent->get('action')->setData($action);
                }
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function collect(FormEvent $event)
    {
        if ($this->readonly) {
            return;
        }

        $form = $event->getForm();
        $data = [];

        foreach ($form as $child) {
            if (!$child->get('active')->getData()) {
                continue;
            }

            $data[] = $child->get('action')->getData();
        }

        $event->setData($data);
    }
}
