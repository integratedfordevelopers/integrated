<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Tests\Resolver;

use Integrated\Common\ContentType\Resolver\PriorityResolverBuilder;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolverBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testAddResolver()
    {
        $resolvers = [
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
        ];

        $builder = $this->getInstance();

        $builder->addResolver($resolvers[6], -1);
        $builder->addResolver($resolvers[4]);
        $builder->addResolver($resolvers[5]);
        $builder->addResolver($resolvers[1], 50);
        $builder->addResolver($resolvers[3], 1);
        $builder->addResolver($resolvers[2], 50);
        $builder->addResolver($resolvers[0], 100);

        self::assertSame($resolvers, $builder->getResolver()->getResolvers());
    }

    public function testAddResolverReplacing()
    {
        $resolvers = [
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
        ];

        $builder = $this->getInstance();

        $builder->addResolver($resolvers[0], -2);
        $builder->addResolver($resolvers[1], 0);
        $builder->addResolver($resolvers[2], 2);

        self::assertSame(array_reverse($resolvers), $builder->getResolver()->getResolvers());

        $builder->addResolver($resolvers[2]);
        $builder->addResolver($resolvers[0], 1);

        self::assertSame($resolvers, $builder->getResolver()->getResolvers());
    }

    public function testGetResolver()
    {
        self::assertInstanceOf('Integrated\\Common\\ContentType\\Resolver\\PriorityResolver', $this->getInstance()->getResolver());
    }

    /**
     * @return PriorityResolverBuilder
     */
    protected function getInstance()
    {
        return new PriorityResolverBuilder();
    }

    /**
     * @return ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver()
    {
        return $this->createMock('Integrated\\Common\\ContentType\\ResolverInterface');
    }
}
