<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\RelationType;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationTypeTest extends TypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DoctrineTestHelper::createTestEntityManager();
    }

    protected function createRegistryMock($name, $em)
    {
        $registry = $this->getMockBuilder('Doctrine\Persistence\ManagerRegistry')->getMock();
        $registry->expects($this->any())
            ->method('getManager')
            ->with($this->equalTo($name))
            ->will($this->returnValue($em));

        return $registry;
    }

    /**
     * @dataProvider getValidTestData
     *
     * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
     *
     * @param array $data
     */
    public function testSubmitValidData(array $data)
    {
        $form = $this->factory->create(RelationType::class, new Relation());
        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertInstanceOf('\Integrated\Bundle\ContentBundle\Document\Relation\Relation', $form->getData());

        $children = $form->createView()->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @return array
     */
    public function getValidTestData()
    {
        return [
            [
                'data1' => [
                    'name' => 'Relation with no sources and targets',
                    'type' => 'type',
                    'sources' => new ArrayCollection(),
                    'targets' => new ArrayCollection(),
                    'multiple' => false,
                    'required' => true,
                ],
                'data2' => [
                    'name' => 'Relation with  sources and targets',
                    'type' => 'type',
                    'sources' => new ArrayCollection([
                        $this->createMock('Integrated\Common\ContentType\ContentTypeInterface'),
                    ]),
                    'targets' => new ArrayCollection([
                        $this->createMock('Integrated\Common\ContentType\ContentTypeInterface'),
                    ]),
                ],
            ],
        ];
    }
}
