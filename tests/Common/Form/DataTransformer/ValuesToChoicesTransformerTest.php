<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Form\DataTransformer;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;

use Integrated\Common\Form\DataTransformer\ValuesToChoicesTransformer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ValuesToChoicesTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChoiceListInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $choiceList;

    protected function setUp()
    {
        $this->choiceList = $this->createMock('Symfony\\Component\\Form\\ChoiceList\\ChoiceListInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Symfony\\Component\\Form\\DataTransformerInterface', $this->getInstance());
    }

    public function testTransform()
    {
        $values = ['value1', 'value2'];
        $choices = ['choice1', 'choice2'];

        $this->choiceList->expects($this->once())
            ->method('getChoicesForValues')
            ->with($this->equalTo($values))
            ->willReturn($choices);

        self::assertSame($choices, $this->getInstance()->transform($values));
    }

    public function testTransformEmpty()
    {
        self::assertEquals([], $this->getInstance()->transform(null));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testTransformInvalidType()
    {
        $this->getInstance()->transform('this-is-not-a-array');
    }

    public function testReverseTransform()
    {
        $choices = ['choice1', 'choice2'];
        $values = ['value1', 'value2'];

        $this->choiceList->expects($this->once())
            ->method('getValuesForChoices')
            ->with($this->equalTo($choices))
            ->willReturn($values);

        self::assertSame($values, $this->getInstance()->reverseTransform($choices));
    }

    public function testReverseTransformEmpty()
    {
        self::assertEquals([], $this->getInstance()->reverseTransform(null));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testReverseTransformInvalidType()
    {
        $this->getInstance()->reverseTransform('this-is-not-a-array');
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testReverseTransformInvalidConversion()
    {
        $this->choiceList->expects($this->once())
            ->method('getValuesForChoices')
            ->willReturn([]);

        $this->getInstance()->reverseTransform(['choice1', 'choice2']);
    }

    /**
     * @return ValuesToChoicesTransformer
     */
    protected function getInstance()
    {
        return new ValuesToChoicesTransformer($this->choiceList);
    }
}
