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
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\News;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class NewsController extends Controller
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var ChannelContextInterface
     */
    private $context;

    /**
     * @param DocumentManager         $documentManager
     * @param ChannelContextInterface $context
     * @param ContainerInterface      $container
     */
    public function __construct(
        DocumentManager $documentManager,
        ChannelContextInterface $context,
        ContainerInterface $container
    ) {
        $this->documentManager = $documentManager;
        $this->context = $context;
        $this->container = $container;
    }

    /**
     * @return array
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

        $queryBuilder = $this->documentManager->createQueryBuilder(News::class);
        $documents = $queryBuilder
            ->select('contentType', 'slug', 'publishTime', 'title', 'relations')
            ->field('channels.$id')->equals($channel->getId())
            ->field('disabled')->equals(false)
            ->field('publishTime.startDate')->gte(new DateTime('-2 days')) // Only the last 2 days for Google
            ->field('publishTime.startDate')->lte($now)
            ->field('publishTime.endDate')->gte($now)
            ->addOr($queryBuilder->expr()->field('primaryChannel.$id')->equals($channel->getId()))
            ->addOr($queryBuilder->expr()->field('primaryChannel')->exists(false))
            ->sort('createdAt', 'desc')
            ->limit(1000)
            ->getQuery()
            ->getIterator();

        return [
            'channel' => $channel,
            'locale' => $this->getParameter('locale'),
            'documents' => $documents,
        ];
    }
}
