<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Solr\Type;

use Integrated\Bundle\SolrBundle\Solr\Type\RemoveType;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RemoveTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeInterface', $this->getInstance());
    }

    /**
     *
     */
    public function testBuild()
    {
        $container = $this->getMock('Integrated\\Common\\Converter\\ContainerInterface');
        $container->expects($this->exactly(3))
            ->method('remove')
            ->withConsecutive([$this->equalTo('field1')], [$this->equalTo('field2')], [$this->equalTo('field3')]);

        $this->getInstance()->build($container, new \stdClass(), ['field1', 'field2', 'field3']);
    }


    /**
     *
     */
    public function testGetName()
    {
        self::assertEquals('integrated.remove', $this->getInstance()->getName());
    }

    /**
     * @return RemoveType
     */
    protected function getInstance()
    {
        return new RemoveType();
    }
}
