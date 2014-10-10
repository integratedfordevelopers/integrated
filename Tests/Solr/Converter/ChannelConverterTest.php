<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Solr\Converter;

use Integrated\Bundle\ContentBundle\Solr\Converter\ChannelConverter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\SolrConverterChannelConverterPass;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Integrated\Common\Solr\Converter\ConverterSpecificationResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var \Symfony\Component\ExpressionLanguage\ExpressionLanguage | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $expressionLanguage;

    /**
     * @var ChannelConverter
     */
    protected $channelConverter;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->resolver = $this->getMock('Integrated\Common\Solr\Converter\ConverterSpecificationResolverInterface');
        $this->expressionLanguage = $this->getMock('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
        $this->channelConverter = new ChannelConverter($this->resolver, $this->expressionLanguage);
    }

    /**
     * Test if converter implements ConverterInterface
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Integrated\Common\Solr\Converter\ConverterInterface', $this->channelConverter);
    }

    public function testGetFieldsWithNonChannelObject()
    {
        /** @var \Integrated\Common\Solr\Converter\ConverterSpecification | \PHPUnit_Framework_MockObject_MockObject $specification */
        $specification = $this->getMock('Integrated\Common\Solr\Converter\ConverterSpecification');

        // Stub getFields
        $specification
            ->expects($this->once())
            ->method('getFields')
            ->will($this->returnValue(array('field1' => null)))
        ;

        // Stub hasField
        $specification
            ->expects($this->once())
            ->method('hasField')
            ->with('field1')
            ->will($this->returnValue(true))
        ;

        // Stub getSpecification
        $this->resolver->expects($this->once())
            ->method('getSpecification')
            ->will($this->returnValue($specification));

        $object = new \stdClass();
        $fields = $this->channelConverter->getFields($object);

        $this->assertArrayHasKey('field1', $fields);
        $this->assertCount(1, $fields);
    }

    public function testGetFieldsWithChannelsAlreadySet()
    {
        /** @var \Integrated\Common\Solr\Converter\ConverterSpecification | \PHPUnit_Framework_MockObject_MockObject $specification */
        $specification = $this->getMock('Integrated\Common\Solr\Converter\ConverterSpecification');

        // Stub getFields
        $specification
            ->expects($this->once())
            ->method('getFields')
            ->will($this->returnValue(array('facet_channels' => array())))
        ;

        // Stub hasField
        $specification
            ->expects($this->once())
            ->method('hasField')
            ->with('facet_channels')
            ->will($this->returnValue(true))
        ;

        // Stub getSpecification
        $this->resolver->expects($this->once())
            ->method('getSpecification')
            ->will($this->returnValue($specification));

        /** @var \Integrated\Common\Content\ChannelableInterface | \PHPUnit_Framework_MockObject_MockObject  $channel */
        $channel = $this->getMock('Integrated\Common\Content\ChannelableInterface');
        $fields = $this->channelConverter->getFields($channel);

        $this->assertArrayHasKey('facet_channels', $fields);
        $this->assertCount(1, $fields);
    }
}
