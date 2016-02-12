<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\ContentType;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RoutingLoader implements LoaderInterface
{
    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var \Integrated\Bundle\PageBundle\Document\Page\ContentTypePage
     */
    protected $page;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Page loader is already added');
        }

        $routes = new RouteCollection();

        $pages = $this->dm->getRepository('IntegratedPageBundle:Page\ContentTypePage')->findAll();

        /** @var \Integrated\Bundle\PageBundle\Document\Page\ContentTypePage $page */
        foreach ($pages as $page) {
            if (!$page->getControllerService()) {
                continue;
            }

            $this->page = $page;

            //todo solr extension maken voor inschieten van urls in solr, gepostfixes met channel (url_dzg)
            //todo twig functie schrijven: integrated_url(document), moet met solr en document overweg kunnen.
            $route = new Route(
                $page->getRoutePath(),
                ['_controller' => sprintf('%s:%s', $page->getControllerService(), $page->getControllerAction())],
                [],
                [],
                '',
                [],
                [],
                'request.attributes.get("_channel") == "' . $page->getChannel()->getId() . '"'
            );

            $routes->add('integrated_website_content_type_page_' . $page->getId(), $route);
        }

        return $routes;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getContentTypeRepo()
    {
        return $this->dm->getRepository('IntegratedContentBundle:ContentType\ContentType');
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'page' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
