<?php

namespace Integrated\Bundle\WorkflowBundle\EventListener\Marker;

use Integrated\Bundle\UserBundle\Model\User;

use Solarium\Core\Event\PreExecute;
use Solarium\QueryType\Select\Query\Query;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MarkerEvent
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
    public function preExecute(PreExecute $event)
    {
        $query = $event->getQuery();

        if (!$query instanceof MarkerInterface) {
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
                $fq->getQuery() . ' OR security_workflow_read: ((%1%))',
                [implode(') OR (', $filterWorkflow)]
            );
        }

        // always allow access to assinged content
        $fq->setQuery($fq->getQuery() . ' OR facet_workflow_assigned_id: %1%', [$user->getId()]);

        if ($user instanceof User) {
            if ($person = $user->getRelation()) {
                $fq->setQuery($fq->getQuery() . ' OR author: %1%*', [$person->getId()]);
            }
        }

        return $query;
    }
}
