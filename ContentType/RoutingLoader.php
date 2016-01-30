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
            dump($page->getPath());
            $this->page = $page;
            $path = preg_replace_callback('/(#)([\s\S]+?)(#)/', [$this, 'convertPath'], $page->getPath());
            dump($path);

            $route = new Route(
                $page->getPath(),
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
        die;
        return $routes;
    }

    protected function convertPath($matches)
    {
        //todo find right url
        $contentType = $this->getContentTypeRepo()->find('');
//        /** @var \Integrated\Bundle\ContentBundle\Document\Relation\Relation $relation */
//        foreach ($relations as $relation) {
//            if ($relation->getId() === $matches[2])
//            {
//
//            }
//        }

        dump($matches);
        dump($this->page);
        return $matches[2];
    }

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
