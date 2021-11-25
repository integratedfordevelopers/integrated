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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageLoader extends Loader
{
    const ROUTE_PREFIX = 'integrated_website_content_type_page';

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var UrlResolver
     */
    protected $urlResolver;

    /**
     * @param DocumentManager $dm
     * @param UrlResolver     $urlResolver
     */
    public function __construct(DocumentManager $dm, UrlResolver $urlResolver)
    {
        $this->dm = $dm;
        $this->urlResolver = $urlResolver;
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

        $pages = $this->dm->getRepository(ContentTypePage::class)->findAll();

        /** @var \Integrated\Bundle\PageBundle\Document\Page\ContentTypePage $page */
        foreach ($pages as $page) {
            if (!$page->getControllerService()) {
                continue;
            }

            $route = new Route(
                $this->urlResolver->getRoutePath($page),
                ['_controller' => sprintf('%s:%s', $page->getControllerService(), $page->getControllerAction()), 'page' => $page->getId()],
                [],
                [],
                '',
                [],
                [],
                'request.attributes.get("_channel") == "'.$page->getChannel()->getId().'"'
            );

            $routes->add($this->urlResolver->getRouteName($page), $route);
        }
        $this->loaded = true;

        return $routes;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Repository\DocumentRepository
     */
    protected function getContentTypeRepo()
    {
        return $this->dm->getRepository(ContentType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return self::ROUTE_PREFIX === $type;
    }
}
