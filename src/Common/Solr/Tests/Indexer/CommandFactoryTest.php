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

use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\ConverterInterface;
use Integrated\Common\Solr\Indexer\CommandFactory;
use Integrated\Common\Solr\Indexer\CommandFactoryInterface;
use Integrated\Common\Solr\Indexer\JobInterface;
use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Command\Delete;
use Solarium\QueryType\Update\Query\Command\Optimize;
use Solarium\QueryType\Update\Query\Command\Rollback;
use Solarium\QueryType\Update\Query\Document\Document;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CommandFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConverterInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $converter;

    /**
     * @var SerializerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    protected function setUp(): void
    {
        $this->converter = $this->createMock(ConverterInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(CommandFactoryInterface::class, $this->getInstance());
    }

    public function testCreateNoAction()
    {
        $this->expectException(\Integrated\Common\Solr\Exception\OutOfBoundsException::class);

        $this->getInstance()->create($this->getJob());
    }

    public function testCreateInvalidAction()
    {
        $this->expectException(\Integrated\Common\Solr\Exception\OutOfBoundsException::class);

        $this->getInstance()->create($this->getJob('does-not-compute'));
    }

    /**
     * @dataProvider createAddProvider
     */
    public function testCreateAdd(array $options, array $expected)
    {
        $document = new \stdClass();

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($this->identicalTo('data'), $this->identicalTo('class'), $this->identicalTo('format'))
            ->willReturn($document);

        $this->converter->expects($this->once())
            ->method('convert')
            ->with($this->identicalTo($document))
            ->willReturn($this->getContainer(['key' => 'value']));

        /** @var Add $result */
        /** @var Document $document */
        $result = $this->getInstance()->create($this->getJob('ADD', [
            'document.data' => 'data',
            'document.class' => 'class',
            'document.format' => 'format',
        ] + $options));

        self::assertInstanceOf(Add::class, $result);
        self::assertCount(1, $result->getDocuments());
        self::assertSame($expected, $result->getOptions());

        $document = current($result->getDocuments());

        self::assertInstanceOf(Document::class, $document);
        self::assertEquals(['key' => 'value'], $document->getFields());
    }

    /**
     * @return array
     */
    public function createAddProvider()
    {
        return [
            'no options' => [
                [],
                [],
            ],
            'options' => [
                ['overwrite' => true, 'commitwithin' => true],
                ['overwrite' => true, 'commitwithin' => true],
            ],
            'only overwrite' => [
                ['overwrite' => true],
                ['overwrite' => true],
            ],
            'only commitwithin' => [
                ['commitwithin' => true],
                ['commitwithin' => true],
            ],
            'none bool' => [
                ['overwrite' => 1, 'commitwithin' => 0],
                ['overwrite' => true, 'commitwithin' => false],
            ],
            'invalid' => [
                ['invalid-option' => 'invalid-value'],
                [],
            ],
        ];
    }

    public function testCreateAddMissingOptions()
    {
        $instance = $this->getInstance();

        self::assertNull($instance->create($this->getJob('ADD')));
        self::assertNull($instance->create($this->getJob('ADD', ['document.data' => 'data'])));
        self::assertNull($instance->create($this->getJob('ADD', ['document.class' => 'class'])));
        self::assertNull($instance->create($this->getJob('ADD', ['document.format' => 'format'])));
    }

    public function testCreateAddNoDeserialize()
    {
        $this->serializer->expects($this->exactly(2))
            ->method('deserialize')
            ->with($this->identicalTo('data'), $this->identicalTo('class'), $this->identicalTo('format'))
            ->willReturn(null);

        $this->converter->expects($this->exactly(2))
            ->method('convert')
            ->with($this->identicalTo(null))
            ->willReturn($this->getContainer());

        $result = $this->getInstance()->create($this->getJob('ADD', [
            'document.data' => 'data',
            'document.class' => 'class',
            'document.format' => 'format',
        ]));

        self::assertNull($result);

        /** @var Delete $result */
        $result = $this->getInstance()->create($this->getJob('ADD', [
            'document.data' => 'data',
            'document.class' => 'class',
            'document.format' => 'format',
            'document.id' => 'id',
        ]));

        self::assertInstanceOf(Delete::class, $result);
        self::assertEquals(['id'], $result->getIds());
        self::assertEmpty($result->getQueries());

        $result = $this->getInstance()->create($this->getJob('ADD', [
            'document.id' => 'id',
        ]));

        self::assertInstanceOf(Delete::class, $result);
        self::assertEquals(['id'], $result->getIds());
        self::assertEmpty($result->getQueries());
    }

    public function testCreateAddDeserializeError()
    {
        $this->expectException(\Integrated\Common\Solr\Exception\SerializerException::class);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->willThrowException(new \Exception());

        $this->getInstance()->create($this->getJob('ADD', [
            'document.data' => 'data',
            'document.class' => 'class',
            'document.format' => 'format',
        ]));
    }

    public function testCreateAddConverterError()
    {
        $this->expectException(\Integrated\Common\Solr\Exception\ConverterException::class);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(null);

        $this->converter->expects($this->once())
            ->method('convert')
            ->willThrowException(new \Exception());

        $this->getInstance()->create($this->getJob('ADD', [
            'document.data' => 'data',
            'document.class' => 'class',
            'document.format' => 'format',
        ]));
    }

    /**
     * @dataProvider createDeleteProvider
     */
    public function testCreateDelete(array $options, array $ids, array $queries)
    {
        $result = $this->getInstance()->create($this->getJob('DELETE', $options));

        self::assertInstanceOf(Delete::class, $result);
        self::assertEquals($ids, $result->getIds());
        self::assertEquals($queries, $result->getQueries());
    }

    /**
     * @return array
     */
    public function createDeleteProvider()
    {
        return [
            'id' => [
                ['id' => 'id'],
                ['id'],
                [],
            ],
            'query' => [
                ['query' => 'query'],
                [],
                ['query'],
            ],
            'both' => [
                ['id' => 'id', 'query' => 'query'],
                ['id'],
                ['query'],
            ],
            'extra invalid' => [
                ['id' => 'id', 'query' => 'query', 'invalid-option' => 'invalid-value'],
                ['id'],
                ['query'],
            ],
        ];
    }

    public function testCreateDeleteInvalid()
    {
        $instance = $this->getInstance();

        self::assertNull($instance->create($this->getJob('DELETE')));
        self::assertNull($instance->create($this->getJob('DELETE', ['invalid-option' => 'invalid-value'])));
    }

    /**
     * @dataProvider createOptimizeProvider
     */
    public function testCreateOptimize(array $options, array $expected)
    {
        $result = $this->getInstance()->create($this->getJob('OPTIMIZE', $options));

        self::assertInstanceOf(Optimize::class, $result);
        self::assertSame($expected, $result->getOptions());
    }

    /**
     * @return array
     */
    public function createOptimizeProvider()
    {
        return [
            'no options' => [
                [],
                [],
            ],
            'options' => [
                ['maxsegments' => true, 'waitsearcher' => true, 'softcommit' => true],
                ['maxsegments' => true, 'waitsearcher' => true, 'softcommit' => true],
            ],
            'only maxsegments' => [
                ['maxsegments' => true],
                ['maxsegments' => true],
            ],
            'only waitsearcher' => [
                ['waitsearcher' => true],
                ['waitsearcher' => true],
            ],
            'only softcommit' => [
                ['softcommit' => true],
                ['softcommit' => true],
            ],
            'none bool' => [
                ['maxsegments' => 1, 'waitsearcher' => 0, 'softcommit' => 0],
                ['maxsegments' => true, 'waitsearcher' => false, 'softcommit' => false],
            ],
            'invalid' => [
                ['invalid-option' => 'invalid-value'],
                [],
            ],
        ];
    }

    /**
     * @dataProvider createCommitProvider
     */
    public function testCreateCommit(array $options, array $expected)
    {
        $result = $this->getInstance()->create($this->getJob('COMMIT', $options));

        self::assertInstanceOf(Commit::class, $result);
        self::assertSame($expected, $result->getOptions());
    }

    /**
     * @return array
     */
    public function createCommitProvider()
    {
        return [
            'no options' => [
                [],
                [],
            ],
            'options' => [
                ['waitsearcher' => true, 'softcommit' => true, 'expungedeletes' => true],
                ['waitsearcher' => true, 'softcommit' => true, 'expungedeletes' => true],
            ],
            'only waitsearcher' => [
                ['waitsearcher' => true],
                ['waitsearcher' => true],
            ],
            'only softcommit' => [
                ['softcommit' => true],
                ['softcommit' => true],
            ],
            'only expungedeletes' => [
                ['expungedeletes' => true],
                ['expungedeletes' => true],
            ],
            'none bool' => [
                ['waitsearcher' => 1, 'softcommit' => 0, 'expungedeletes' => 0],
                ['waitsearcher' => true, 'softcommit' => false, 'expungedeletes' => false],
            ],
            'invalid' => [
                ['invalid-option' => 'invalid-value'],
                [],
            ],
        ];
    }

    public function testCreateRollback()
    {
        $result = $this->getInstance()->create($this->getJob('ROLLBACK'));

        self::assertInstanceOf(Rollback::class, $result);
        self::assertEmpty($result->getOptions());

        $result = $this->getInstance()->create($this->getJob('ROLLBACK', ['invalid-option' => 'invalid-value']));

        self::assertInstanceOf(Rollback::class, $result);
        self::assertEmpty($result->getOptions());
    }

    /**
     * @return CommandFactory
     */
    public function getInstance()
    {
        return new CommandFactory($this->converter, $this->serializer);
    }

    /**
     * @param string $action
     * @param array  $options
     *
     * @return JobInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getJob($action = null, array $options = [])
    {
        $mock = $this->createMock(JobInterface::class);
        $mock->expects($this->atLeastOnce())
            ->method('hasAction')
            ->willReturn($action ? true : false);

        $mock->expects($this->any())
            ->method('getAction')
            ->willReturn($action ?: null);

        $hasOption = function ($key) use ($options) {
            return isset($options[$key]);
        };

        $mock->expects($this->any())
            ->method('hasOption')
            ->willReturnCallback($hasOption);

        $getOption = function ($key) use ($options) {
            return isset($options[$key]) ? $options[$key] : null;
        };

        $mock->expects($this->any())
            ->method('getOption')
            ->willReturnCallback($getOption);

        return $mock;
    }

    /**
     * @param array $data
     *
     * @return ContainerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContainer(array $data = [])
    {
        $mock = $this->createMock(ContainerInterface::class);
        $mock->expects($this->any())
            ->method('count')
            ->willReturn(\count($data));

        $mock->expects($this->any())
            ->method('toArray')
            ->willReturn($data);

        return $mock;
    }
}
