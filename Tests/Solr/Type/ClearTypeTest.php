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

use Integrated\Bundle\SolrBundle\Solr\Type\ClearType;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClearTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeInterface', $this->getInstance());
    }

    public function testBuild()
    {
        $container = $this->getMock('Integrated\\Common\\Converter\\ContainerInterface');
        $container->expects($this->once())
            ->method('clear');

        $this->getInstance()->build($container, new \stdClass());
    }


    public function testGetName()
    {
        self::assertEquals('integrated.clear', $this->getInstance()->getName());
    }

    /**
     * @return ClearType
     */
    protected function getInstance()
    {
        return new ClearType();
    }
}
