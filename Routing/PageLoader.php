<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageLoader implements LoaderInterface
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

        $pages = $this->dm->getRepository('IntegratedPageBundle:Page\Page')->findAll();
        // @todo publication filters (INTEGRATED-425)

        /** @var \Integrated\Bundle\PageBundle\Document\Page\Page $page */
        foreach ($pages as $page) {
            $condition = '';

            if ($channel = $page->getChannel()) {
                $condition = 'request.attributes.get("_channel") == "' . $channel->getId() . '"';
            }

            $route = new Route(
                $page->getPath(),
                [
                    '_controller' => 'IntegratedWebsiteBundle:Page:show', // @todo config option (INTEGRATED-426)
                    'id' => $page->getId(),
                ],
                [],
                [],
                '',
                [],
                [],
                $condition
            );

            $routes->add('integrated_website_page_' . $page->getId(), $route);
        }

        return $routes;
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
