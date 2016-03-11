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

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Services\RouteResolver;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageLoader extends Loader
{
    const ROUTE_PREFIX = 'integrated_content_type_page';

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var RouteResolver
     */
    protected $routeResolver;

    /**
     * @var \Integrated\Bundle\PageBundle\Document\Page\ContentTypePage
     */
    protected $page;

    /**
     * @param DocumentManager $dm
     * @param RouteResolver $routeResolver
     */
    public function __construct(DocumentManager $dm, RouteResolver $routeResolver)
    {
        $this->dm = $dm;
        $this->routeResolver = $routeResolver;
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
            $this->routeResolver->setContentTypePage($page);

            $route = new Route(
                $this->routeResolver->getRoutePath(),
                ['_controller' => sprintf('%s:%s', $page->getControllerService(), $page->getControllerAction())],
                [],
                [],
                '',
                [],
                [],
                'request.attributes.get("_channel") == "' . $page->getChannel()->getId() . '"'
            );

            $routes->add($this->routeResolver->getRouteName(), $route);
        }
        $this->loaded = true;

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
        return self::ROUTE_PREFIX === $type;
    }
}
