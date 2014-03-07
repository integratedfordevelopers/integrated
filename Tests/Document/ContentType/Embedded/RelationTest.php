<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\ContentType\Document\Embedded;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Relation;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Relation
     */
    private $relation;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->relation = new Relation();
    }

    /**
     * ContentTypeRelation should implement ContentTypeRelationInterface
     */
    public function testInstanceOfContentTypeRelationInterface()
    {
        $this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeRelationInterface', $this->relation);
    }

    /**
     * Test get- and setId function
     */
    public function testGetAndSetIdFunction()
    {
        $id = 'id';
        $this->assertEquals($id, $this->relation->setId($id)->getId());
    }

    /**
     * Test get- and setName function
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertEquals($name, $this->relation->setName($name)->getName());
    }

    /**
     * Test get- and setType function
     */
    public function testGetAndSetTypeFunction()
    {
        $type = 'type';
        $this->assertEquals($type, $this->relation->setType($type)->getType());
    }

    /**
     * Test get- and setContentTypes function
     */
    public function testGetAndSetContentTypesFunction()
    {
        // Mock contentTypes
        $contentType1 = $this->getMockClass('Integrated\Common\ContentType\ContentTypeInterface');
        $contentType2 = $this->getMockClass('Integrated\Common\ContentType\ContentTypeInterface');

        // Create arrayCollection
        $contentTypes = new ArrayCollection(
            array(
                $contentType1,
                $contentType2
            )
        );

        // Asserts
        $this->assertSame($contentTypes, $this->relation->setContentTypes($contentTypes)->getContentTypes());
    }

    /**
     * Test get- and setMultiple function
     */
    public function testGetAndSetMultipleFunction()
    {
        $multiple = true;
        $this->assertEquals($multiple, $this->relation->setMultiple($multiple)->getMultiple());
    }

    /**
     * Test get- and setRequired function
     */
    public function testGetAndSetRequiredFunction()
    {
        $required = true;
        $this->assertEquals($required, $this->relation->setRequired($required)->getRequired());
    }
}