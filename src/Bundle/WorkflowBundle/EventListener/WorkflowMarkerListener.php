<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\EventListener;

use Integrated\Bundle\UserBundle\Model\User;
use Solarium\Core\Event;
use Solarium\QueryType\Select\Query\Query;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class WorkflowMarkerListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Event\Events::PRE_EXECUTE => 'preExecute',
        ];
    }

    /**
     * @param Event\PreExecute $event
     */
    public function preExecute(Event\PreExecute $event)
    {
        $query = $event->getQuery();

        if (!$query instanceof WorkflowMarkerInterface) {
            return;
        }

        if (!$query instanceof Query) {
            throw new \InvalidArgumentException(sprintf('$query must be of type %s', Query::class));
        }

        $user = null;
        if ($token = $this->tokenStorage->getToken()) {
            $user = $token->getUser();
        }

        $filterWorkflow = [];
        if ($user instanceof User) {
            foreach ($user->getGroups() as $group) {
                $filterWorkflow[] = $group->getId();
            }
        }

        $fq = $query->createFilterQuery('workflow');

        // allow content without workflow
        $fq
            ->addTag('workflow')
            ->addTag('security')
            ->setQuery('(*:* -security_workflow_read:[* TO *])')
        ;

        // allow content with group access
        if ($filterWorkflow) {
            $fq->setQuery(
                $fq->getQuery().' OR security_workflow_read: ((%1%))',
                [implode(') OR (', $filterWorkflow)]
            );
        }

        // always allow access to assinged content
        $fq->setQuery($fq->getQuery().' OR facet_workflow_assigned_id: %1%', [$user->getId()]);

        if ($user instanceof User) {
            if ($person = $user->getRelation()) {
                $fq->setQuery($fq->getQuery().' OR author: %1%*', [$person->getId()]);
            }
        }
    }
}
