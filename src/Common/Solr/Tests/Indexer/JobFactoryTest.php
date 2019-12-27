<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Indexer;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Solr\Indexer\JobFactory;
use Integrated\Common\Solr\Indexer\JobFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JobFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SerializerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    /**
     * @var string
     */
    private $format = 'json';

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(JobFactoryInterface::class, $this->getInstance());
    }

    /**
     * @dataProvider createAddProvider
     */
    public function testCreateAdd($action, ContentInterface $content, $id, $class, $format)
    {
        $this->format = $format;

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($this->identicalTo($content), $this->equalTo($format))
            ->willReturn('this-is-the-data');

        $job = $this->getInstance()->create($action, $content);

        self::assertEquals('ADD', $job->getAction());

        self::assertEquals($id, $job->getOption('document.id'));
        self::assertEquals('this-is-the-data', $job->getOption('document.data'));
        self::assertEquals($class, $job->getOption('document.class'));
        self::assertEquals($format, $job->getOption('document.format'));
    }

    /**
     * @return array
     */
    public function createAddProvider()
    {
        return [
            'lower case' => [
                'add',
                new \Integrated\Common\Solr\Tests\Fixtures\Object1(),
                'type1-id1',
                'Integrated\\Common\\Solr\\Tests\\Fixtures\\Object1',
                'json',
            ],
            'upper case' => [
                'ADD',
                new \Integrated\Common\Solr\Tests\Fixtures\Object1(),
                'type1-id1',
                'Integrated\\Common\\Solr\\Tests\\Fixtures\\Object1',
                'json',
            ],
            'doctrine proxy' => [
                'add',
                new \Integrated\Common\Solr\Tests\Fixtures\__CG__\ProxyObject(),
                'proxy-type-proxy-id',
                'ProxyObject', // everything before and including __GC__ should be stripped
                'json',
            ],
            'format' => [
                'ADD',
                new \Integrated\Common\Solr\Tests\Fixtures\Object2(),
                'type2-id2',
                'Integrated\\Common\\Solr\\Tests\\Fixtures\\Object2',
                'xml',
            ],
        ];
    }

    /**
     * @dataProvider createDeleteProvider
     */
    public function testCreateDelete($action, ContentInterface $content, $id)
    {
        $job = $this->getInstance()->create($action, $content);

        self::assertEquals('DELETE', $job->getAction());
        self::assertEquals($id, $job->getOption('id'));
    }

    /**
     * @return array
     */
    public function createDeleteProvider()
    {
        return [
            'lower case' => [
                'delete',
                new \Integrated\Common\Solr\Tests\Fixtures\Object1(),
                'type1-id1',
            ],
            'upper case' => [
                'DELETE',
                new \Integrated\Common\Solr\Tests\Fixtures\Object2(),
                'type2-id2',
            ],
        ];
    }

    public function testCreateInvalidAction()
    {
        $this->expectException(\Integrated\Common\Solr\Exception\OutOfBoundsException::class);

        $this->getInstance()->create('none-existing-action', $this->getContent());
    }

    /**
     * @return JobFactory
     */
    public function getInstance()
    {
        return new JobFactory($this->serializer, $this->format);
    }

    /**
     * @return ContentInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContent()
    {
        return $this->createMock(ContentInterface::class);
    }
}
