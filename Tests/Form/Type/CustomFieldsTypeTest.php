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

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Bundle\ContentBundle\Form\Type\CustomFieldsType;

use Integrated\Common\ContentType\ContentTypeInterface;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldsTypeTest extends TypeTestCase
{
    /**
     * @var CustomFieldsType
     */
    protected $type;

    /**
     * Setup the test
     */
    public function setup()
    {
        $this->type = new CustomFieldsType();
        parent::setUp();
    }

    /**
     * @dataProvider getValidData
     * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
     * @param array $data
     */
    public function testSubmitValidData(array $data)
    {
        $form = $this->factory->create($this->type, [], ['contentType' => $this->getContentType()]);

        $form->submit($data);
        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;
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
                'data' => [
                    'customField1' => 'Data for customField1',
                    'customField2' => 'Data for customField2',
                ],
                'data' => [
                    'customField1' => null,
                    'customField2' => 'Data for customField2'
                ]
            ]
        ];
    }

    /**
     * @return ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContentType()
    {
        /** @var ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject $contentType */
        $contentType = $this->getMock(ContentTypeInterface::class);

        /** @var Field|\PHPUnit_Framework_MockObject_MockObject $defaultField */
        $defaultField = $this->getMock(Field::class);

        /** @var CustomField|\PHPUnit_Framework_MockObject_MockObject $customField1 */
        $customField1 = $this->getMock(CustomField::class);

        /** @var CustomField|\PHPUnit_Framework_MockObject_MockObject $customField2 */
        $customField2 = $this->getMock(CustomField::class);

        // Stub the customField getters so we can check the outcome
        $customField1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('customField1')
        ;

        $customField1
            ->expects($this->once())
            ->method('getType')
            ->willReturn('text')
        ;

        $customField1
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([])
        ;

        $customField2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('customField2')
        ;

        $customField2
            ->expects($this->once())
            ->method('getType')
            ->willReturn('textarea')
        ;

        $customField2
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(['required' => true])
        ;

        $fields = [$defaultField, $customField1, $customField2];
        $contentType
            ->expects($this->once())
            ->method('getFields')
            ->willReturn($fields)
        ;

        return $contentType;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtensions()
    {
        $validator = $this->getMock('\Symfony\Component\Validator\Validator\ValidatorInterface');
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));
        $validator->method('getMetadataFor')->willReturn($this->getMock('\Symfony\Component\Validator\Mapping\ClassMetadata', [], [], '', false));

        return [new ValidatorExtension($validator)];
    }
}
