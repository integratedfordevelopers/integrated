<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\ContentBundle\Tests\Twig\Extension;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\CustomField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\DocumentField;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\RelationField;
use Integrated\Bundle\ContentBundle\Twig\Extension\FormConfigExtension;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use stdClass as Object;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Twig_SimpleFilter;

class FormConfigExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetFilters()
    {
        $filters = (new FormConfigExtension())->getFilters();

        $this->assertCount(2, $filters);
        $this->assertContainsOnlyInstancesOf(Twig_SimpleFilter::class, $filters);
    }

    public function testName()
    {
        $extention = new FormConfigExtension();

        $this->assertEquals('', $extention->name($this->createMock(FormConfigFieldInterface::class)));

        $config = $this->getMockBuilder(DocumentField::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEquals('Content', $extention->name($config));

        $config = $this->getMockBuilder(RelationField::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEquals('Relation', $extention->name($config));

        $config = $this->getMockBuilder(CustomField::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(TextareaType::class);

        $this->assertEquals('Custom (textarea)', $extention->name($config));

        $config = $this->getMockBuilder(CustomField::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(TextType::class);

        $this->assertEquals('Custom (text)', $extention->name($config));

        $config = $this->getMockBuilder(CustomField::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn('');

        $this->assertEquals('', $extention->name($config));
    }

    /**
     * @dataProvider labelProvider
     */
    public function testLabel(string $name, array $options, string $expected)
    {
        $config = $this->createMock(FormConfigFieldInterface::class);
        $config->expects($this->atLeastOnce())
            ->method('getOptions')
            ->willReturn($options);

        $config->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $this->assertEquals($expected, (new FormConfigExtension())->label($config));
    }

    public function labelProvider()
    {
        return [
            ['name 1', ['label' => 'Label 1'], 'Label 1'],
            ['name 2', ['label' => null], 'Name 2'],
            ['Name 3', [], 'Name 3'],
            ['name_4', [], 'Name 4'],
            ['_name_5_', [], 'Name 5'],
            [' Name_6 ', [], 'Name 6'],
        ];
    }

    public function testLabelNoField()
    {
        $this->assertEquals('', (new FormConfigExtension())->label(new Object()));
    }
}
