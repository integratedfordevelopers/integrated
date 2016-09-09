<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentHistoryBundle\Diff\ArrayComparer;
use Integrated\Bundle\ContentHistoryBundle\Doctrine\ODM\MongoDB\Persister\PersistenceBuilder;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Integrated\Bundle\ContentHistoryBundle\Document\Embedded\User;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistorySubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function onFlush($args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        $this->createLog($dm, $uow->getScheduledDocumentInsertions(), ContentHistory::ACTION_INSERT);
        $this->createLog($dm, $uow->getScheduledDocumentUpdates(), ContentHistory::ACTION_UPDATE);
        $this->createLog($dm, $uow->getScheduledDocumentDeletions(), ContentHistory::ACTION_DELETE);
    }

    /**
     * @param DocumentManager $dm
     * @param array $documents
     * @param string $action
     */
    protected function createLog(DocumentManager $dm, array $documents, $action)
    {
        $uow = $dm->getUnitOfWork();
        $pb = new PersistenceBuilder($dm);

        foreach ($documents as $document) {
            if (!$document instanceof ContentInterface) {
                continue;
            }

            $history = new ContentHistory($document->getId(), $action);

            // load original data
            $originalData = (array) $dm->createQueryBuilder(get_class($document))->hydrate(false)
                ->field('id')->equals($document->getId())
                ->getQuery()->getSingleResult();

            switch ($action) {
                case ContentHistory::ACTION_INSERT:
                    $history->setChangeSet($pb->prepareData($document));
                    break;

                case ContentHistory::ACTION_UPDATE:
                    $history->setChangeSet(ArrayComparer::diff($originalData, $pb->prepareData($document)));
                    break;

                case ContentHistory::ACTION_DELETE:
                    $history->setChangeSet($originalData);
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Invalid action "%s"', $action));
                    break;
            }

            if (!count($history->getChangeSet())) {
                continue; // no changes
            }

            $history->setUser($this->getUser());

            $dm->persist($history);
            $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($history)), $history);
        }
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        $user = new User();
        $token = $this->container->get('security.token_storage')->getToken();

        if ($token instanceof TokenInterface) {
            $securityUser = $token->getUser();

            if ($securityUser instanceof \Integrated\Bundle\UserBundle\Model\User) {
                $user->setId($securityUser->getId());
                $user->setName($securityUser->getUsername());

                $relation = $securityUser->getRelation();

                if ($relation instanceof ContentInterface && $name = (string) $relation) {
                    // override with a better name
                    $user->setName($name);
                }
            }
        }

        $requestStack = $this->container->get('request_stack');

        if ($requestStack instanceof RequestStack) {
            $request = $requestStack->getMasterRequest();

            if ($request instanceof Request) {
                $user->setIpAddress($request->getClientIp());
                $user->setEndpoint($request->getSchemeAndHttpHost() . $request->getRequestUri());
            }
        }

        return $user;
    }
}
