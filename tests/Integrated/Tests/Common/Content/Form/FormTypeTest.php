<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Form;

use Integrated\Common\Content\Form\FormType;
use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormTypeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ContentTypeInterface | \PHPUnit_Framework_MockObject_MockObject
	 */
	private $type;

	/**
	 * @var FormType
	 */
	private $form;

	protected function setUp()
	{
		$this->type = $this->getMock('Integrated\Common\ContentType\ContentTypeInterface');
		$this->form = new FormType($this->type);

	}

	public function testInterface()
	{
		$this->assertInstanceOf('Integrated\Common\Content\Form\FormTypeInterface', $this->form);
	}

	public function testBuildForm()
	{
		$this->markTestIncomplete();
	}

	public function testSetDefaultOptions()
	{
		$this->markTestIncomplete();
	}

	public function testGetParent()
	{
		$this->assertEquals('form', $this->form->getParent());
	}

	public function testGetType()
	{
		$this->assertSame($this->type, $this->form->getType());
	}

	public function testGetName()
	{
		$this->type->expects($this->any())
			->method('getClass')
			->will($this->returnValue('Integrated\Common\Content\ContentInterface'));

		$this->type->expects($this->any())
			->method('getType')
			->will($this->returnValue('type'));

		$this->assertEquals('integrated_common_content_contentinterface__type', $this->form->getName());
	}
}