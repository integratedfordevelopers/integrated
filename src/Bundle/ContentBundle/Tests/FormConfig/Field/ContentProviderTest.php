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

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\DocumentField;
use Integrated\Bundle\ContentBundle\FormConfig\Field\ContentProvider;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Form\Mapping\AttributeInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class ContentProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MetadataFactoryInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $metadata;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->metadata = $this->createMock(MetadataFactoryInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFieldProviderInterface::class, new ContentProvider($this->metadata));
    }

    public function testGetFields()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->once())
            ->method('getClass')
            ->willReturn('class_name');

        $metadata = $this->createMock(MetadataInterface::class);
        $metadata->expects($this->once())
            ->method('getFields')
            ->willReturn([
                $this->getField('name1', 'type1', ['key1' => 'value1']),
                $this->getField('name2', 'type2', ['key2' => 'value2']),
                $this->getField('name3', 'type3', ['key3' => 'value3']),
                $this->getField('name1', 'type1', ['key1' => 'value1']),
            ]);

        $this->metadata->expects($this->once())
            ->method('getMetadata')
            ->with($this->equalTo('class_name'))
            ->willReturn($metadata);

        $fields = (new ContentProvider($this->metadata))->getFields($type);

        $this->assertCount(4, $fields);
        $this->assertContainsOnlyInstancesOf(DocumentField::class, $fields);
    }

    public function getField(string $name, string $type, array $options): AttributeInterface
    {
        $field = $this->createMock(AttributeInterface::class);
        $field->expects($this->once())
            ->method('getName')
            ->willReturn($name);

        $field->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $field->expects($this->once())
            ->method('getOptions')
            ->willReturn($options);

        return $field;
    }
}
