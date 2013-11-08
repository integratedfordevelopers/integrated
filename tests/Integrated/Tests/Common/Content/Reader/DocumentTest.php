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

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Integrated\Common\ContentType\Mapping\Metadata\ContentTypeFactory;
use Integrated\Common\Content\Reader\Document;
use Integrated\Common\ContentType\Mapping\Metadata;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadataFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataFactory;

    /**
     * @var ContentTypeFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $contentTypeFactory;

    /**
     * @var Document
     */
    private $document;


    protected function setUp()
    {
        // Mock ClassMetadataFactory
        $this->metadataFactory = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');

        // Mock ContentTypeFactory
        $this->contentTypeFactory = $this->getMock('Integrated\Common\ContentType\Mapping\Metadata\ContentTypeFactory', array(), array(), '', false);

        // Create document
        $this->document = new Document($this->metadataFactory, $this->contentTypeFactory);
    }

    public function testReadAllFunction()
    {
        // Mock ContentInterface
        $content1 = $this->getMockClass('Integrated\Common\Content\ContentInterface', array(), array(), 'content1');
        $content2 = $this->getMockClass('Integrated\Common\Content\ContentInterface', array(), array(), 'content2');

        // Mock ClassMetadata
        $metadata1 = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($content1));

        $metadata2 = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($content2));

        // Stub getAllMetaData function
        $this->metadataFactory->expects($this->once())
            ->method('getAllMetaData')
            ->will($this->returnValue(array($metadata1, $metadata2)));


        $this->contentTypeFactory->expects($this->any())
            ->method('build')
            ->will($this->returnValue('Metadata'));

        // Asserts
        $this->assertCount(2, $this->document->readAll());
    }

    public function testReadAllFunctionWithDuplicateClassName()
    {
        // Mock ContentInterface
        $content1 = $this->getMockClass('Integrated\Common\Content\ContentInterface', array(), array(), 'content1');
        $content2 = $this->getMockClass('Integrated\Common\Content\ContentInterface', array(), array(), 'content1');

        // Mock ClassMetadata
        $metadata1 = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($content1));

        $metadata2 = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($content2));

        // Stub getAllMetaData function
        $this->metadataFactory->expects($this->once())
            ->method('getAllMetaData')
            ->will($this->returnValue(array($metadata1, $metadata2)));


        $this->contentTypeFactory->expects($this->any())
            ->method('build')
            ->will($this->returnValue('Metadata'));

        // Asserts
        $this->assertCount(1, $this->document->readAll());


    }

    private function getMetadata($count = 5)
    {
        $content = array();
        $metadata = array();

        for ($i = 0; $i < $count; $i++) {

            $content[$i] = $this->getMockClass('Integrated\Common\Content\ContentInterface', array(), array(), 'content_' . $i);

            // Mock ClassMetadata
            $metadata[$i] = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
            $metadata[$i]->expects($this->exactly(2))->method('getName')->will($this->returnValue($content[$i]));
        }

        return $metadata;
    }
}