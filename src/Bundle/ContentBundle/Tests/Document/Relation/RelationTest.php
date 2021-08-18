<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Relation;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Relation must implement RelationInterface.
     */
    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\Relation\RelationInterface', $this->getInstance());
    }

    /**
     * Test if all the default values are set.
     */
    public function testDefaultValues()
    {
        $instance = $this->getInstance();
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $instance->getTargets());
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $instance->getSources());
        $this->assertInstanceOf('\DateTime', $instance->getCreatedAt());
    }

    /**
     * Test the get- and setId function.
     */
    public function testGetAndSetIdFunction()
    {
        $instance = $this->getInstance();

        $id = 'id';

        $this->assertSame($instance, $instance->setId($id));
        $this->assertEquals($id, $instance->getId());
    }

    /**
     * Test get- and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $instance = $this->getInstance();

        $name = 'name';

        $this->assertSame($instance, $instance->setName($name));
        $this->assertEquals($name, $instance->getName());
    }

    /**
     * Test get- and setType function.
     */
    public function testGetAndSetTypeFunction()
    {
        $instance = $this->getInstance();

        $type = 'type';

        $this->assertSame($instance, $instance->setType($type));
        $this->assertEquals($type, $instance->getType());
    }

    /**
     * Test get- and setSources function with valid collection.
     *
     * @param ArrayCollection $collection
     * @dataProvider validCollectionProvider
     */
    public function testGetAndSetSourcesFunctionWithValidCollection(ArrayCollection $collection)
    {
        $instance = $this->getInstance();

        $this->assertSame($instance, $instance->setSources($collection));
        $this->assertEquals($collection, $instance->getSources());
    }

    /**
     * Test get- and setSources function with invalid collection.
     *
     * @param ArrayCollection $collection
     * @dataProvider invalidCollectionProvider
     */
    public function testGetAndSetSourcesFunctionWithInvalidCollection(ArrayCollection $collection)
    {
        $this->expectException(\TypeError::class);

        $instance = $this->getInstance();
        $instance->setSources($collection);
    }

    /**
     * Test addSource function with duplicate source.
     */
    public function testAddSourceFunctionWithDuplicateSource()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Common\ContentType\ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $source */
        $source = $this->createMock('Integrated\Common\ContentType\ContentTypeInterface');

        $instance->addSource($source);
        $collection = $instance->getSources();
        $instance->addSource($source);

        $this->assertSame($collection, $instance->getSources());
    }

    /**
     * Test removeSource function with existing source.
     */
    public function testRemoveSourceFunctionWithExistingSource()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Common\ContentType\ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $source */
        $source = $this->createMock('Integrated\Common\ContentType\ContentTypeInterface');

        $instance->addSource($source);

        $this->assertTrue($instance->removeSource($source));
    }

    /**
     * Test removeSource function with non existing source.
     */
    public function testRemoveSourceFunctionWithNonExistingSource()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Common\ContentType\ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $source */
        $source = $this->createMock('Integrated\Common\ContentType\ContentTypeInterface');

        $this->assertFalse($instance->removeSource($source));
    }

    /**
     * Test get- and setTargets function with valid collection.
     *
     * @param ArrayCollection $collection
     * @dataProvider validCollectionProvider
     */
    public function testGetAndSetTargetsFunctionWithValidCollection(ArrayCollection $collection)
    {
        $instance = $this->getInstance();

        $this->assertSame($instance, $instance->setTargets($collection));
        $this->assertEquals($collection, $instance->getTargets());
    }

    /**
     * Test get- and setTargets function with invalid collection.
     *
     * @param ArrayCollection $collection
     * @dataProvider invalidCollectionProvider
     */
    public function testGetAndSetTargetsFunctionWithInvalidCollection(ArrayCollection $collection)
    {
        $this->expectException(\TypeError::class);

        $instance = $this->getInstance();
        $instance->setTargets($collection);
    }

    /**
     * Test addTarget function with duplicate target.
     */
    public function testAddTargetFunctionWithDuplicateTarget()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Common\ContentType\ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->createMock('Integrated\Common\ContentType\ContentTypeInterface');

        $instance->addTarget($target);
        $collection = $instance->getTargets();
        $instance->addTarget($target);

        $this->assertSame($collection, $instance->getTargets());
    }

    /**
     * Test removeTarget function with existing target.
     */
    public function testRemoveTargetFunctionWithExistingTarget()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Common\ContentType\ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->createMock('Integrated\Common\ContentType\ContentTypeInterface');

        $instance->addTarget($target);

        $this->assertTrue($instance->removeTarget($target));
    }

    /**
     * Test removeTarget function with non existing source.
     */
    public function testRemoveTargetFunctionWithNonExistingSource()
    {
        $instance = $this->getInstance();

        /** @var \Integrated\Common\ContentType\ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $source */
        $source = $this->createMock('Integrated\Common\ContentType\ContentTypeInterface');

        $this->assertFalse($instance->removeTarget($source));
    }

    /**
     * Test is- and setMultiple function.
     */
    public function testIsAndSetMultipleFunction()
    {
        $instance = $this->getInstance();

        $multiple = false;

        $this->assertSame($instance, $instance->setMultiple($multiple));
        $this->assertEquals($multiple, $instance->isMultiple());
    }

    /**
     * Test is- and setRequired function.
     */
    public function testIsAndSetRequiredFunction()
    {
        $instance = $this->getInstance();

        $required = true;

        $this->assertSame($instance, $instance->setRequired($required));
        $this->assertEquals($required, $instance->isRequired());
    }

    /**
     * Test get- and setCreatedAt function.
     */
    public function testGetAndSetCreatedAtFunction()
    {
        $instance = $this->getInstance();

        $createdAt = new \DateTime();

        $this->assertSame($instance, $instance->setCreatedAt($createdAt));
        $this->assertEquals($createdAt, $instance->getCreatedAt());
    }

    /**
     * @return array
     */
    public function validCollectionProvider()
    {
        return [
            [
                new ArrayCollection([
                    $this->createMock('Integrated\Common\ContentType\ContentTypeInterface'),
                    $this->createMock('Integrated\Common\ContentType\ContentTypeInterface'),
                ]),
            ],
            [
                new ArrayCollection([
                    $this->createMock('Integrated\Common\ContentType\ContentTypeInterface'),
                ]),
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidCollectionProvider()
    {
        return [
            [
                new ArrayCollection(['Invalid', true, ['types']]),
            ],
        ];
    }

    /**
     * @return Relation
     */
    protected function getInstance()
    {
        return new Relation();
    }
}
