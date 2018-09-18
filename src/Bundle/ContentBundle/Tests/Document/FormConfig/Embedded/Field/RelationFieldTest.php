<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\FormConfig\Embedded\Field;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\RelationField;
use Integrated\Common\Content\Relation\RelationInterface;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RelationFieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RelationInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $relation;

    /**
     * @var RelationField
     */
    private $field;

    protected function setUp()
    {
        $this->relation = $this->createMock(RelationInterface::class);
        $this->field = new RelationField($this->relation, ['option1' => 'value1', 'option2' => 'value2']);
    }

    public function testIterface()
    {
        $this->assertInstanceOf(FormConfigFieldInterface::class, $this->field);
    }

    public function testName()
    {
        $this->relation->expects($this->once())
            ->method('getName')
            ->willReturn('name');

        $this->assertEquals('name', $this->field->getName());
    }

    public function testType()
    {
        $this->assertEquals(HiddenType::class, $this->field->getType());
    }

    public function testOptions()
    {
        $this->assertEquals(['option1' => 'value1', 'option2' => 'value2'], $this->field->getOptions());
    }

    public function testRelation()
    {
        $this->assertSame($this->relation, $this->field->getRelation());
    }
}
