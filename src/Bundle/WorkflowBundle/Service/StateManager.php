<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;

class StateManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * State constructor.
     *
     * @param EntityManager   $entityManager
     * @param DocumentManager $documentManager
     */
    public function __construct(EntityManager $entityManager, DocumentManager $documentManager)
    {
        $this->entityManager = $entityManager;
        $this->documentManager = $documentManager;
    }

    /**
     * @param string $contentType
     *
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function ensureWorkflowState(string $contentType)
    {
        $contentType = $this->documentManager->getRepository(ContentType::class)->find($contentType);

        if (!$contentType || !$contentType->hasOption('workflow')) {
            return;
        }

        $workflow = $this->entityManager->getRepository(Definition::class)->find($contentType->getOption('workflow'));
        if (!$workflow) {
            return;
        }

        $defaultState = $workflow->getDefault();

        $contentIds = $this->documentManager->createQueryBuilder(Content::class)
            ->select('_id', 'class')
            ->field('contentType')->equals($contentType->getId())
            ->hydrate(false)
            ->getQuery()
            ->execute();

        foreach ($contentIds as $content) {
            if (!$this->entityManager->getRepository(State::class)->findOneBy(['content_id' => $content['_id'], 'content_class' => $content['class']])) {
                $content = $this->documentManager->getRepository(Content::class)->find($content['_id']);

                // is disabled field state same as published state? we don't want to change the disabled state without an item review
                if ($content->isDisabled() != $defaultState->isPublishable()) {
                    // explicitly assign new state to article
                    $state = new State();
                    $state->setContent($content);
                    $state->setState($defaultState);

                    $this->entityManager->persist($state);
                    $this->entityManager->flush();
                }
            }
        }
    }
}
