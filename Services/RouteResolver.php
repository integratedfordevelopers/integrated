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

use Integrated\Bundle\WebsiteBundle\Routing\ContentTypePageLoader;
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
     * @var array
     */
    protected $relations = [];

    /**
     * RouteResolver constructor.
     * @param DocumentManager $dm
     * @param Router $router
     */
    public function __construct(DocumentManager $dm, Router $router)
    {
        $this->dm = $dm;
    }

    /**
     * Returns the correct path for symfony routing module
     * @return string
     */
    public function getRoutePath()
    {
        return $this->matchRelations();
    }

    /**
     * Converts the relations in the path to route syntax and registers the matched relations
     * @return string
     */
    protected function matchRelations()
    {
        return preg_replace_callback(
            '/(#)([\s\S]+?)(#)/',
            function ($matches) {
                $this->relations[$matches[2]] = $matches[2];
                return sprintf('{%s}', $matches[2]);
            },
            $this->getContentTypePage()->getPath()
        );
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return sprintf('%s_%s', ContentTypePageLoader::ROUTE_PREFIX, $this->getContentTypePage()->getId());
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
