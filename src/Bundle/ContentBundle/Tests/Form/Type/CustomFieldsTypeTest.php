<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Form\Type;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Bundle\ContentBundle\Form\Type\CustomFieldsType;
use Integrated\Common\ContentType\ContentTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldsTypeTest extends TypeTestCase
{
    /**
     * @dataProvider getValidData
     *
     * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
     *
     * @param array $data
     */
    public function testSubmitValidData(array $data)
    {
        $form = $this->factory->create(CustomFieldsType::class, [], ['contentType' => $this->getContentType()]);
        $form->submit($data);

        $this->assertTrue($form->isSynchronized());

        $children = $form->createView()->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @return array
     */
    public function getValidData()
    {
        return [
            [
                'data1' => [
                    'customField1' => 'Data for customField1',
                    'customField2' => 'Data for customField2',
                    'customField3' => true,
                ],
                'data2' => [
                    'customField1' => null,
                    'customField2' => 'Data for customField2',
                    'customField3' => false,
                ],
            ],
        ];
    }

    /**
     * @return ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContentType()
    {
        /** @var ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $contentType */
        $contentType = $this->createMock(ContentTypeInterface::class);

        /** @var Field|\PHPUnit_Framework_MockObject_MockObject $defaultField */
        $defaultField = $this->createMock(Field::class);

        /** @var CustomField|\PHPUnit_Framework_MockObject_MockObject $customField1 */
        $customField1 = $this->createMock(CustomField::class);

        /** @var CustomField|\PHPUnit_Framework_MockObject_MockObject $customField2 */
        $customField2 = $this->createMock(CustomField::class);

        /** @var CustomField|\PHPUnit_Framework_MockObject_MockObject $customField3 */
        $customField3 = $this->createMock(CustomField::class);

        // Stub the customField getters so we can check the outcome
        $customField1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('customField1');

        $customField1
            ->expects($this->once())
            ->method('getType')
            ->willReturn(TextType::class);

        $customField1
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);

        $customField2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('customField2');

        $customField2
            ->expects($this->once())
            ->method('getType')
            ->willReturn(TextareaType::class);

        $customField2
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(['required' => true]);

        $customField3
            ->expects($this->once())
            ->method('getName')
            ->willReturn('customField3');

        $customField3
            ->expects($this->once())
            ->method('getType')
            ->willReturn(CheckboxType::class);

        $customField3
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(['required' => true]);

        $contentType
            ->expects($this->once())
            ->method('getFields')
            ->willReturn([$defaultField, $customField1, $customField2, $customField3]);

        return $contentType;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtensions()
    {
        $validator = $this->createMock('\Symfony\Component\Validator\Validator\ValidatorInterface');
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));
        $validator->method('getMetadataFor')->willReturn($this->getMockBuilder('\Symfony\Component\Validator\Mapping\ClassMetadata')->disableOriginalConstructor()->getMock());

        return [new ValidatorExtension($validator)];
    }
}
