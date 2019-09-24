<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Routing\Tests;

use Integrated\Common\Routing\Router;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router as SymfonyRouter;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RouterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RouterInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    protected $router;

    /**
     * @var UrlGeneratorInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    protected $generator;

    /**
     * @var RequestContext
     */
    protected $context;

    protected function setUp()
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->generator = $this->createMock(UrlGeneratorInterface::class);
        $this->context = $this->createMock(RequestContext::class);
    }

    public function testInterface()
    {
        $router = $this->getInstance();

        self::assertInstanceOf(RouterInterface::class, $router);
        self::assertInstanceOf(RequestMatcherInterface::class, $router);
    }

    public function testAddAndContext()
    {
        $router = $this->getInstance();

        self::assertSame($this->context, $router->getContext());

        $context = $this->createMock(RequestContext::class);
        $router->setContext($context);

        self::assertSame($context, $router->getContext());

        $this->context = null;

        $router = $this->getInstance();

        self::assertInstanceOf(RequestContext::class, $router->getContext());
    }

    public function testMatch()
    {
        $this->router->expects($this->atLeastOnce())
            ->method('setContext')
            ->with($this->identicalTo($this->context));

        $response = [
            new stdClass(),
            new stdClass(),
            new stdClass(),
        ];

        $this->router->expects($this->exactly(3))
            ->method('match')
            ->withConsecutive(
                [$this->equalTo('path1')],
                [$this->equalTo('path2')],
                [$this->equalTo('path3')]
            )
            ->willReturnOnConsecutiveCalls($response[0], $response[1], $response[2]);

        $router = $this->getInstance();

        self::assertSame($response[0], $router->match('path1'));
        self::assertSame($response[1], $router->match('path2'));
        self::assertSame($response[2], $router->match('path3'));
    }

    public function testMatchRequest()
    {
        // use the symfony router as mock as that class also implements
        // the RequestMatcherInterface interface.

        $this->router = $this->getMockBuilder(SymfonyRouter::class)->disableOriginalConstructor()->getMock();
        $this->router->expects($this->atLeastOnce())
            ->method('setContext')
            ->with($this->identicalTo($this->context));

        $response = [
            new stdClass(),
            new stdClass(),
            new stdClass(),
        ];

        $request = [
            $this->getRequest(),
            $this->getRequest(),
            $this->getRequest(),
        ];

        $this->router->expects($this->exactly(3))
            ->method('matchRequest')
            ->withConsecutive(
                [$this->identicalTo($request[0])],
                [$this->identicalTo($request[1])],
                [$this->identicalTo($request[2])]
            )
            ->willReturnOnConsecutiveCalls($response[0], $response[1], $response[2]);

        $router = $this->getInstance();

        self::assertSame($response[0], $router->matchRequest($request[0]));
        self::assertSame($response[1], $router->matchRequest($request[1]));
        self::assertSame($response[2], $router->matchRequest($request[2]));
    }

    public function testMatchRequestWithOutRequestMatcherInterface()
    {
        $this->router->expects($this->atLeastOnce())
            ->method('setContext')
            ->with($this->identicalTo($this->context));

        $response = [
            new stdClass(),
            new stdClass(),
            new stdClass(),
        ];

        $request = [
            $this->getRequest('path1'),
            $this->getRequest('path2'),
            $this->getRequest('path3'),
        ];

        $this->router->expects($this->exactly(3))
            ->method('match')
            ->withConsecutive(
                [$this->equalTo('path1')],
                [$this->equalTo('path2')],
                [$this->equalTo('path3')]
            )
            ->willReturnOnConsecutiveCalls($response[0], $response[1], $response[2]);

        $router = $this->getInstance();

        self::assertSame($response[0], $router->matchRequest($request[0]));
        self::assertSame($response[1], $router->matchRequest($request[1]));
        self::assertSame($response[2], $router->matchRequest($request[2]));
    }

    /**
     * @param string $name
     * @param array  $param
     * @param int    $type
     * @param string $return
     *
     * @dataProvider createGenerate
     */
    public function testGenerate($name, $param, $type, $return)
    {
        $this->generator->expects($this->atLeastOnce())
            ->method('setContext')
            ->with($this->identicalTo($this->context));

        $this->generator->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($name), $this->equalTo($param), $this->equalTo($type))
            ->willReturn($return);

        self::assertEquals($return, $this->getInstance()->generate($name, $param, $type));
    }

    /**
     * @return array
     */
    public function createGenerate()
    {
        return [
            [
                'name1',
                ['param1.1', 'param1.2'],
                UrlGeneratorInterface::ABSOLUTE_URL,
                'return1',
            ],
            [
                'name2',
                null,
                UrlGeneratorInterface::ABSOLUTE_PATH,
                'return2',
            ],
            [
                'name3',
                ['param3.1', 'param3.2'],
                UrlGeneratorInterface::RELATIVE_PATH,
                'return3',
            ],
            [
                'name4',
                ['param4.1', 'param4.2'],
                UrlGeneratorInterface::NETWORK_PATH,
                'return4',
            ],
        ];
    }

    public function testGetRouteCollection()
    {
        $response = [
            new stdClass(),
            new stdClass(),
            new stdClass(),
        ];

        $this->router->expects($this->exactly(3))
            ->method('getRouteCollection')
            ->willReturnOnConsecutiveCalls($response[0], $response[1], $response[2]);

        $router = $this->getInstance();

        self::assertSame($response[0], $router->getRouteCollection());
        self::assertSame($response[1], $router->getRouteCollection());
        self::assertSame($response[2], $router->getRouteCollection());
    }

    public function testGetMatcher()
    {
        $this->router->expects($this->atLeastOnce())
            ->method('setContext')
            ->with($this->identicalTo($this->context));

        self::assertSame($this->router, $this->getInstance()->getMatcher());
    }

    public function testGetGenerator()
    {
        $this->generator->expects($this->atLeastOnce())
            ->method('setContext')
            ->with($this->identicalTo($this->context));

        self::assertSame($this->generator, $this->getInstance()->getGenerator());
    }

    /**
     * @return Router
     */
    protected function getInstance()
    {
        return new Router($this->router, $this->generator, $this->context);
    }

    /**
     * @param string $path
     *
     * @return Request | \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRequest($path = null)
    {
        $mock = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();

        if ($path) {
            $mock->expects($this->atLeastOnce())
                ->method('getPathInfo')
                ->willReturn($path);
        }

        return $mock;
    }
}
