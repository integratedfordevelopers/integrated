<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Model\GroupInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\Permission;
use Integrated\Bundle\WorkflowBundle\Form\DataTransformer\PermissionTransformer;
use Integrated\Common\Security\PermissionInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PermissionTransformerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private $repository;

    protected function setup(): void
    {
        $this->repository = $this->getMockBuilder(ObjectRepository::class)->getMock();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\\Component\\Form\\DataTransformerInterface', $this->getInstance());
    }

    public function testTransform()
    {
        $permission1 = new Permission();
        $permission1->setGroup('group-1');
        $permission1->addMask(PermissionInterface::READ);
        $permission1->addMask(PermissionInterface::WRITE);

        $permission2 = new Permission();
        $permission2->setGroup('group-2');
        $permission2->addMask(PermissionInterface::READ);

        $permission3 = new Permission();
        $permission3->setGroup('group-3');
        $permission3->addMask(PermissionInterface::WRITE);

        $permission4 = new Permission();
        $permission4->setGroup('group-4');

        $group1 = $this->getGroup('group-1');
        $group2 = $this->getGroup('group-2');
        $group3 = $this->getGroup('group-3');

        $this->repository
            ->expects($this->exactly(4))
            ->method('findOneBy')
            ->withConsecutive(
                [$this->equalTo(['id' => 'group-1'])],
                [$this->equalTo(['id' => 'group-2'])],
                [$this->equalTo(['id' => 'group-3'])],
                [$this->equalTo(['id' => 'group-4'])]
            )
            ->willReturnOnConsecutiveCalls(
                $group1,
                $group2,
                $group3,
                null
            );

        $result = $this->getInstance()->transform([$permission1, $permission2, $permission3, $permission4]);

        $this->assertCount(2, $result['read']);
        $this->assertCount(2, $result['write']);

        $this->assertContains($group1, $result['read']);
        $this->assertContains($group2, $result['read']);

        $this->assertContains($group1, $result['write']);
        $this->assertContains($group3, $result['write']);
    }

    public function testTransformInvalidType()
    {
        $this->expectException(\Symfony\Component\Form\Exception\TransformationFailedException::class);

        $this->getInstance()->transform('invalid');
    }

    public function testTransformInvalidArrayContentType()
    {
        $this->expectException(\Symfony\Component\Form\Exception\TransformationFailedException::class);

        $this->getInstance()->transform(['invalid', 'invalid']);
    }

    public function testTransformEmpty()
    {
        $expected = [
            'read' => [],
            'write' => [],
        ];

        $this->assertEquals($expected, $this->getInstance()->transform(''));
        $this->assertEquals($expected, $this->getInstance()->transform(null));
        $this->assertEquals($expected, $this->getInstance()->transform([]));
        $this->assertEquals($expected, $this->getInstance()->transform(new ArrayCollection()));
    }

    public function testReverseTransform()
    {
        $group1 = $this->getGroup('group-1');
        $group2 = $this->getGroup('group-2');
        $group3 = $this->getGroup('group-3');

        $result = $this->getInstance()->reverseTransform(['read' => [$group1, $group2], 'write' => [$group1, $group3, $group3]]);

        $this->assertInstanceOf('Doctrine\\Common\\Collections\\Collection', $result);
        $this->assertCount(3, $result);

        $ids = [];

        foreach ($result as $object) {
            $this->assertInstanceOf('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\Permission', $object);

            /** @var Permission $object */
            switch ($object->getGroup()) {
                case 'group-1':
                    $this->assertTrue($object->hasMask(PermissionInterface::READ));
                    $this->assertTrue($object->hasMask(PermissionInterface::WRITE));
                    break;

                case 'group-2':
                    $this->assertTrue($object->hasMask(PermissionInterface::READ));
                    $this->assertFalse($object->hasMask(PermissionInterface::WRITE));
                    break;

                case 'group-3':
                    $this->assertFalse($object->hasMask(PermissionInterface::READ));
                    $this->assertTrue($object->hasMask(PermissionInterface::WRITE));
                    break;
            }

            $ids[] = $object->getGroup();
        }

        $this->assertContains('group-1', $ids);
        $this->assertContains('group-2', $ids);
        $this->assertContains('group-3', $ids);
    }

    public function testReverseTransformEmpty()
    {
        $result = $this->getInstance()->reverseTransform('');

        $this->assertInstanceOf('Doctrine\\Common\\Collections\\Collection', $result);
        $this->assertCount(0, $result);

        $result = $this->getInstance()->reverseTransform(null);

        $this->assertInstanceOf('Doctrine\\Common\\Collections\\Collection', $result);
        $this->assertCount(0, $result);

        $result = $this->getInstance()->reverseTransform([]);

        $this->assertInstanceOf('Doctrine\\Common\\Collections\\Collection', $result);
        $this->assertCount(0, $result);

        $result = $this->getInstance()->reverseTransform(['read' => [], 'write' => []]);

        $this->assertInstanceOf('Doctrine\\Common\\Collections\\Collection', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @return PermissionTransformer
     */
    protected function getInstance()
    {
        return new PermissionTransformer($this->repository);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|ObjectRepository
     */
    protected function getRepository()
    {
        return $this->getMockBuilder(ObjectRepository::class)->getMock();
    }

    /**
     * @param $id
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|GroupInterface
     */
    protected function getGroup($id)
    {
        $group = $this->getMockBuilder(GroupInterface::class)->getMock();
        $group
            ->expects($this->any())
            ->method('getId')
            ->willReturn($id)
        ;

        return $group;
    }
}
