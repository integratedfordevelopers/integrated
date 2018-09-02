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

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\UserBundle\Model\Scope;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class ScopeEventSubscriber
{
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $channel = $eventArgs->getDocument();
        if (!$channel instanceof Channel) {
            return;
        }

        $dm = $eventArgs->getDocumentManager();

        $property = $dm->getClassMetadata(\get_class($channel))->reflClass->getProperty('scope');
        $property->setAccessible(true);

        if (!$id = $property->getValue($channel)) {
            return;
        }

        $property = $dm->getClassMetadata(\get_class($channel))->reflClass->getProperty('scopeInstance');
        $property->setAccessible(true);
        $property->setValue(
            $channel,
            $this->entityManager->getReference(Scope::class, $id)
        );
    }
}
