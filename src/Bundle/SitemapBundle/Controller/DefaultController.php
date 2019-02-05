<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SitemapBundle\Controller;

use DateTime;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DefaultController extends Controller
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var ChannelContextInterface
     */
    private $context;

    /**
     * @param ManagerRegistry         $registry
     * @param ChannelContextInterface $context
     * @param ContainerInterface      $container
     */
    public function __construct(
        ManagerRegistry $registry,
        ChannelContextInterface $context,
        ContainerInterface $container
    ) {
        $this->registry = $registry;
        $this->context = $context;
        $this->container = $container;
    }

    /**
     * @return array
     *
     * @Template
     *
     * @throws \Exception
     */
    public function indexAction()
    {
        $channel = $this->context->getChannel();

        if (!$channel) {
            throw new NotFoundHttpException('No channel found');
        }

        $now = new DateTime();

        $queryBuilder = $this->registry->getManagerForClass(Content::class)->createQueryBuilder(Content::class);
        $count = $queryBuilder
            ->field('channels.$id')->equals($channel->getId())
            ->field('disabled')->equals(false)
            ->field('publishTime.startDate')->lte($now)
            ->field('publishTime.endDate')->gte($now)
            ->addOr($queryBuilder->expr()->field('primaryChannel.$id')->equals($channel->getId()))
            ->addOr($queryBuilder->expr()->field('primaryChannel')->exists(false))
            ->getQuery()
            ->count();

        if (!$count) {
            throw new NotFoundHttpException();
        }

        return [
            'count' => min(ceil($count / 50000), 50000),
        ];
    }

    /**
     * @param $page
     *
     * @return array
     *
     * @Template
     *
     * @throws \Exception
     */
    public function listAction($page)
    {
        $channel = $this->context->getChannel();

        if (!$channel) {
            throw new NotFoundHttpException('No channel found');
        }

        $page = (int) $page;

        if ($page != min(max($page, 1), 50000)) {
            throw new NotFoundHttpException();
        }

        $now = new DateTime();

        $documents = $this->registry->getManagerForClass(Content::class)->createQueryBuilder(Content::class)
            ->select('contentType', 'slug', 'createdAt', 'class')
            ->field('channels.$id')->equals($channel->getId())
            ->field('disabled')->equals(false)
            ->field('publishTime.startDate')->lte($now)
            ->field('publishTime.endDate')->gte($now)
            ->sort('_id')
            ->skip(--$page * 50000)
            ->limit(50000)
            ->getQuery()
            ->getIterator();

        return [
            'documents' => $documents,
        ];
    }
}
