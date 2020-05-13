<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content\Embedded;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Relation
     */
    private $relation;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->relation = new Relation();
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $this->relation->getReferences());
    }

    /**
     * Test get- and setReferences functions.
     */
    public function testGetAndSetReferencesFunction()
    {
        $references = new ArrayCollection(['ref1']);
        $this->assertEquals($references, $this->relation->setReferences($references)->getReferences());
    }

    /**
     * Test addReferences function.
     */
    public function testAddReferencesFunction()
    {
        // Create references and add them
        $references = new ArrayCollection(
            [
                $this->createMock('\Integrated\Common\Content\ContentInterface'),
            ]
        );

        $this->relation->addReferences($references);

        // Asserts
        $this->assertEquals($references, $this->relation->getReferences());
    }

    /**
     * Test addReference functions.
     */
    public function testAddReferenceFunction()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->createMock('\Integrated\Common\Content\ContentInterface');

        // Asserts
        $this->assertEquals($this->relation, $this->relation->addReference($content));
        $this->assertEquals($content, $this->relation->getReferences()->first());
    }

    /**
     * Test addReference function with duplicate reference.
     */
    public function testAddReferenceFunctionWithDuplicateReference()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->createMock('\Integrated\Common\Content\ContentInterface');

        // Add content two times
        $this->relation->addReference($content)->addReference($content);

        // Asserts
        $this->assertCount(1, $this->relation->getReferences());
    }

    /**
     * Test removeReference function.
     */
    public function testRemoveReferenceFunction()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->createMock('\Integrated\Common\Content\ContentInterface');

        // Add content
        $this->relation->addReference($content);

        // Asserts
        $this->assertTrue($this->relation->removeReference($content));
    }

    /**
     * Test removeReference function with invalid content.
     */
    public function testRemoveReferenceFunctionWithInvalidContent()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->createMock('\Integrated\Common\Content\ContentInterface');

        // Asserts
        $this->assertFalse($this->relation->removeReference($content));
    }
}
