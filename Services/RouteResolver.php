<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Services;

use Symfony\Component\Routing\Router;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\PageBundle\ContentType\RoutingLoader;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RouteResolver
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ContentTypePage
     */
    protected $contentTypePage;

    /**
     * @var Relation[]
     */
    protected $relations = [];

    /**
     * RouteResolver constructor.
     * @param DocumentManager $dm
     * @param Router $router
     */
    public function __construct(DocumentManager $dm,  Router $router)
    {
        $this->dm = $dm;
    }

    /**
     * Returns the correct path for symfony routing module
     * @return mixed
     */
    public function getRoutePath()
    {
        return preg_replace_callback(
            '/(#)([\s\S]+?)(#)/',
            function ($matches) {
                return $matches[2];
            },
            $this->getContentTypePage()->getPath()
        );
    }

    /**
     * @param ContentTypePage $page
     */
    public function resolveRoute()
    {
        return $this->router->generate(
            $this->getRouteName($this->getContentTypePage()->getId()),
        //todo resolve params and relations
            ['slug' => 'a']
        );
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return sprintf('%s_%s', RoutingLoader::ROUTE_PREFIX, $this->getContentTypePage()->getId());
    }

    /**
     * @return ContentTypePage
     */
    public function getContentTypePage()
    {
        return $this->contentTypePage;
    }

    /**
     * @param ContentTypePage $contentTypePage
     * @return $this
     */
    public function setContentTypePage($contentTypePage)
    {
        $this->contentTypePage = $contentTypePage;
        return $this;
    }
}
