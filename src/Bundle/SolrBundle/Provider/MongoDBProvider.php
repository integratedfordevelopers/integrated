<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Provider;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Integrated\Common\Solr\Task\Provider\ContentProviderInterface;
use Integrated\Common\Solr\Task\Provider\ContentTypeProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBProvider implements ContentProviderInterface, ContentTypeProviderInterface
{
    /**
     * @var DocumentRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param DocumentRepository $repository
     */
    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenced($id)
    {
        $iterator = $this->repository->createQueryBuilder()
            ->field('relations.references.$id')->equals($id)
            ->getQuery()
            ->getIterator();

        return $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($id)
    {
        $iterator = $this->repository->createQueryBuilder()
            ->field('contentType')->equals($id)
            ->getQuery()
            ->getIterator();

        return $iterator;
    }
}
