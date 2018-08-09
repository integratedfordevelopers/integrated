<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\ContentType;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContentType
     */
    private $contentType;

    /**
     * Setup the test.
     */
    protected function setUp()
    {
        $this->contentType = new ContentType();
    }

    /**
     * ContentType should implement ContentTypeInterface.
     */
    public function testInstanceOfContentTypeInterface()
    {
        $this->assertInstanceOf('Integrated\Common\ContentType\ContentTypeInterface', $this->contentType);
    }

    /**
     * Test the create functions.
     */
    public function testCreate()
    {
        // Mock ContentInterface
        $content = $this->createMock('Integrated\Common\Content\ContentInterface');

        // Set class
        $class = get_class($content);
        $this->contentType->setClass($class);

        // Assert
        $this->assertInstanceOf($class, $this->contentType->create());
    }

    /**
     * Test get- and setId function.
     */
    public function testGetAndSetIdFunction()
    {
        $id = 'abc123';
        $this->assertEquals($id, $this->contentType->setId($id)->getId());
    }

    /**
     * Test get- and setClass function.
     */
    public function testGetAndSetClassFunction()
    {
        $class = 'HenkDeVries';
        $this->assertEquals($class, $this->contentType->setClass($class)->getClass());
    }

    /**
     * Test get- and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'Henk de Vries';
        $this->assertEquals($name, $this->contentType->setName($name)->getName());
    }

    /**
     * Test get- and setCreatedAt function.
     */
    public function testGetAndSetCreatedAtFunction()
    {
        $createdAt = new \DateTime();
        $this->assertSame($createdAt, $this->contentType->setCreatedAt($createdAt)->getCreatedAt());
    }
}
