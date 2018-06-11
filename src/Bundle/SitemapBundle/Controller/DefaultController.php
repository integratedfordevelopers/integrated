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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\News;
use Integrated\Common\Content\Channel\ChannelContextInterface;

class DefaultController extends Controller
{
    /**
     * @return array
     * @Template
     */
    public function indexAction()
    {
        $channel = $this->getChannel();

        $qb = $this->getDocumentManager()->createQueryBuilder(Content::class)
            ->hydrate(false)
            ->select('contentType', 'slug', 'createdAt', 'class')
            ->field('channels.$id')->equals($channel->getId())
            ->field('disabled')->equals(false)
            ->sort('_id');

        $documents = $qb->getQuery()->execute();

        return [
            'documents' => $documents,
        ];
    }

    /**
     * @return array
     * @Template
     */
    public function newsAction()
    {
        $channel = $this->getChannel();

        $qb = $this->getDocumentManager()->createQueryBuilder(News::class)
            ->select('contentType', 'slug', 'publishTime', 'title', 'relations')
            ->field('channels.$id')->equals($channel->getId())
            ->field('publishTime.startDate')->gte(new \DateTime('-2 days'))
            ->sort('createdAt', 'desc');

        $documents = $qb->getQuery()->execute();

        return [
            'locale' => $this->getParameter('locale'),
            'channel' => $channel,
            'documents' => $documents,
            'resolver' => $this->getUrlResolver(),
        ];
    }

    /**
     * @return array
     * @Template
     */
    public function robotsAction()
    {
        return [];
    }

    /**
     * @return \Integrated\Common\Content\Channel\ChannelInterface
     *
     * @throws \RuntimeException
     */
    protected function getChannel()
    {
        $context = $this->container->get('channel.context');

        if (!$context instanceof ChannelContextInterface) {
            throw new \RuntimeException('Unable to resolve the channel.');
        }

        return $context->getChannel();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * @return \Integrated\Bundle\PageBundle\Services\UrlResolver
     */
    protected function getUrlResolver()
    {
        return $this->get('integrated_page.services.url_resolver');
    }
}
