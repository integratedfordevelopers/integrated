<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Tests\DataTransformer;

use Integrated\Common\Form\DataTransformer\ValueToChoiceTransformer;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ValueToChoiceTransformerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ChoiceListInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $choiceList;

    protected function setUp(): void
    {
        $this->choiceList = $this->createMock('Symfony\\Component\\Form\\ChoiceList\\ChoiceListInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Symfony\\Component\\Form\\DataTransformerInterface', $this->getInstance());
    }

    public function testTransform()
    {
        $this->choiceList->expects($this->once())
            ->method('getChoicesForValues')
            ->with($this->equalTo(['value']))
            ->willReturn(['choice']);

        self::assertSame('choice', $this->getInstance()->transform('value'));
    }

    public function testReverseTransform()
    {
        $this->choiceList->expects($this->once())
            ->method('getValuesForChoices')
            ->with($this->equalTo(['choice']))
            ->willReturn(['value']);

        self::assertSame('value', $this->getInstance()->reverseTransform('choice'));
    }

    public function testReverseTransformEmpty()
    {
        self::assertNull($this->getInstance()->reverseTransform(null));
    }

    public function testReverseTransformInvalidConversion()
    {
        $this->expectException(\Symfony\Component\Form\Exception\TransformationFailedException::class);

        $this->choiceList->expects($this->once())
            ->method('getValuesForChoices')
            ->willReturn([]);

        $this->getInstance()->reverseTransform('choice');
    }

    /**
     * @return ValueToChoiceTransformer
     */
    protected function getInstance()
    {
        return new ValueToChoiceTransformer($this->choiceList);
    }
}
