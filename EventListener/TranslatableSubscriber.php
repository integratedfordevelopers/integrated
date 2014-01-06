<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Clean up references after removal of a document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class TranslatableSubscriber implements EventSubscriber
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'postLoad'
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        // Get document
        $document = $args->getDocument();

        // Document must be instanceof Content
        if ($document instanceof Translatable) {
            foreach ($document->getTranslations() as $locale => $value) {
                if ($locale == $this->translator->getLocale()) {
                    $document->setCurrentTranslation($value);
                    return;
                }
            }
        }
    }
}