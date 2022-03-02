<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Router implements RouterInterface, RequestMatcherInterface, WarmableInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var UrlGeneratorInterface
     */
    protected $generator;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @param RouterInterface       $router
     * @param UrlGeneratorInterface $generator
     * @param RequestContext        $context
     */
    public function __construct(
        RouterInterface $router,
        UrlGeneratorInterface $generator,
        RequestContext $context = null
    ) {
        $this->router = $router;
        $this->generator = $generator;
        $this->context = $context ?: new RequestContext();
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        return $this->getMatcher()->match($pathinfo);
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $matcher = $this->getMatcher();

        if ($matcher instanceof RequestMatcherInterface) {
            return $matcher->matchRequest($request);
        }

        return $matcher->match($request->getPathInfo());
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        return $this->getGenerator()->generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        return $this->router->getRouteCollection();
    }

    /**
     * @return UrlMatcherInterface
     */
    public function getMatcher()
    {
        $this->router->setContext($this->getContext());

        return $this->router;
    }

    /**
     * @return UrlGeneratorInterface
     */
    public function getGenerator()
    {
        $this->generator->setContext($this->getContext());

        return $this->generator;
    }

    public function warmUp(string $cacheDir): array
    {
        return [];
    }
}
