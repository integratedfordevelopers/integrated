<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Form\DataTransformer;

use Integrated\Bundle\WorkflowBundle\Form\DataTransformer\DefinitionTransformer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DefinitionTransformerTest extends \PHPUnit_Framework_TestCase
{
	public function testInterface()
	{
		$this->assertInstanceOf('Symfony\\Component\\Form\\DataTransformerInterface', $this->getInstance());
	}

	public function testTransform()
	{
		$result = $this->getInstance()->transform('id-value');

		$this->assertInternalType('object', $result);
		$this->assertObjectHasAttribute('id', $result);
		$this->assertEquals('id-value', $result->id);
	}

	public function testTransformValidType()
	{
		$this->getInstance()->transform('string');
		$this->getInstance()->transform(10);
	}

	/**
	 * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
	 */
	public function testTransformInvalidType()
	{
		$this->getInstance()->transform([]);
	}

	public function testTransformEmpty()
	{
		$this->assertNull($this->getInstance()->transform(''));
		$this->assertNull($this->getInstance()->transform(null));
	}

	public function testReverseTransform()
	{
		$value = $this->getMock('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');
		$value->expects($this->atLeastOnce())
			->method('getId')
			->willReturn('id-value');

		$this->assertEquals('id-value', $this->getInstance()->reverseTransform($value));
	}

	/**
	 * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
	 */
	public function testReverseTransformInvalidType()
	{
		$this->getInstance()->reverseTransform('string');
	}

	public function testReverseTransformEmpty()
	{
		$this->assertNull($this->getInstance()->reverseTransform(''));
		$this->assertNull($this->getInstance()->reverseTransform(null));
	}

	/**
	 * @return DefinitionTransformer
	 */
	protected function getInstance()
	{
		return new DefinitionTransformer();
	}
}
 