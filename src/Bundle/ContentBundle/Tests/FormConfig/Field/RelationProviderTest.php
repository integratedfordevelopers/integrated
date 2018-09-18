<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\ContentBundle\Tests\FormConfig\Field;

use ArrayIterator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\RelationField;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\FormConfig\Field\RelationProvider;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class RelationProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ManagerRegistry | \PHPUnit\Framework\MockObject\MockObject
     */
    private $registry;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFieldProviderInterface::class, new RelationProvider($this->registry));
    }

    public function testGetFields()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->once())
            ->method('getId')
            ->willReturn('id');

        $iterator = new ArrayIterator([
            $this->createMock(Relation::class),
            $this->createMock(Relation::class),
            $this->createMock(Relation::class),
            $this->createMock(Relation::class)
        ]);

        $query = $this->getMockBuilder(Query::class)->disableOriginalConstructor()->getMock();
        $query->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $builder = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([$this->createMock(DocumentManager::class)])
            ->setMethods(['getQuery'])
            ->getMock();

        $builder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $repository = $this->getMockBuilder(DocumentRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($builder);

        $this->registry->expects($this->once())
            ->method('getRepository')
            ->with(Relation::class)
            ->willReturn($repository);

        $fields = (new RelationProvider($this->registry))->getFields($type);

        $this->assertCount(4, $fields);
        $this->assertContainsOnlyInstancesOf(RelationField::class, $fields);
    }
}
