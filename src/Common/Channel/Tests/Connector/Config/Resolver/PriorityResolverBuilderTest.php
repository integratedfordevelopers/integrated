<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Connector\Config\Resolver;

use Integrated\Common\Channel\Connector\Config\Resolver\PriorityResolverBuilder;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;

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
        ];

        $builder = $this->getInstance();

        $builder->addResolver($resolvers[2]);
        $builder->addResolver($resolvers[1]);
        $builder->addResolver($resolvers[0]);
        $builder->addResolver($resolvers[1]);
        $builder->addResolver($resolvers[2]);

        self::assertSame($resolvers, $builder->getResolver()->getResolvers());
    }

    public function testAddResolverWithDifferentPriorities()
    {
        $resolvers = [
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
        ];

        $builder = $this->getInstance();

        $builder->addResolver($resolvers[0], 0);
        $builder->addResolver($resolvers[1]);
        $builder->addResolver($resolvers[2], -1);
        $builder->addResolver($resolvers[0], 20);

        self::assertSame($resolvers, $builder->getResolver()->getResolvers());
    }

    public function testAddResolvers()
    {
        $resolvers = [
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
        ];

        $builder = $this->getInstance();

        $builder->addResolvers(array_reverse($resolvers));
        $builder->addResolvers($resolvers);

        self::assertSame($resolvers, $builder->getResolver()->getResolvers());
    }

    public function testAddResolversWithDifferentPriorities()
    {
        $resolvers = [
            $this->getResolver(),
            $this->getResolver(),
            $this->getResolver(),
        ];

        $builder = $this->getInstance();

        $builder->addResolvers(array_reverse($resolvers));

        $builder->addResolvers([$resolvers[0]], 0);
        $builder->addResolvers([$resolvers[1]]);
        $builder->addResolvers([$resolvers[2]], -1);
        $builder->addResolvers([$resolvers[0]], 20);

        self::assertSame($resolvers, $builder->getResolver()->getResolvers());
    }

    protected function getInstance()
    {
        return new PriorityResolverBuilder();
    }

    /**
     * @return ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver()
    {
        return $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\ResolverInterface');
    }
}
