<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\EventListener;

use Integrated\Common\Content\ExtensibleInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class UserProfileExtensionListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
	private $name;

	/**
	 * @param $name
	 */
	public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit'
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        if (!$parent = $event->getForm()->getParent()) {
			return;
		}

        $content = $parent->getNormData();

		if ($content instanceof ExtensibleInterface) {
			$event->setData($content->getExtension($this->getName()));
		}
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
		if (!$parent = $event->getForm()->getParent()) {
			return;
		}

		$content = $parent->getNormData();

		if ($content instanceof ExtensibleInterface) {
			$content->setExtension($this->getName(), $event->getForm()->getData()); // should not be required
		}
    }

	/**
	 * @return string
	 */
	protected function getName()
	{
		return $this->name;
	}
}