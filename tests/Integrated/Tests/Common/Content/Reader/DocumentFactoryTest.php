<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Reader;

use Integrated\Common\Content\Reader\Document;
use Integrated\Common\Content\Reader\DocumentFactory;
use Integrated\Common\ContentType\Mapping\Metadata;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DocumentFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the DocumentFactory build function
     */
    public function testBuildFunction()
    {
        // Mock MetadataFactory
        $metadataFactory = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');

        // Mock ObjectManager
        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManager->expects($this->once())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metadataFactory));

        // Mock ManagerRegistry
        $managerRegistry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $managerRegistry->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($objectManager));

        // Mock ContentTypeFactory
        $contentTypeFactory = $this->getMock('Integrated\Common\ContentType\Mapping\Metadata\ContentTypeFactory', array(), array(), '', false);

        // Create factory
        $factory = new DocumentFactory();

        // Assert
        $this->assertInstanceOf('Integrated\Common\Content\Reader\Document', $factory->build($managerRegistry, $contentTypeFactory));
    }
}