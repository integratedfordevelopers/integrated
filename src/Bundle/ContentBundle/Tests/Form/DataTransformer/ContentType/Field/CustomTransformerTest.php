<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Form\DataTransformer\ContentType\Field;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType\Field\CustomTransformer;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomTransformerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomTransformer
     */
    protected $customTransformer;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->customTransformer = new CustomTransformer();
    }

    /**
     * Test instanceOf.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->customTransformer);
    }

    /**
     * Test transform function.
     *
     * @param mixed $input
     * @param array $output
     * @dataProvider getTransformData
     */
    public function testTransformFunction($input, array $output)
    {
        $this->assertSame($output, $this->customTransformer->transform($input));
    }

    /**
     * Test reverseTransform function with invalid data.
     *
     * @param mixed $input
     * @dataProvider getInvalidReverseTransformData
     */
    public function testReverseTransformFunctionWithInvalidData($input)
    {
        $this->assertNull($this->customTransformer->reverseTransform($input));
    }

    /**
     * Test reverseTransform function with valid data.
     *
     * @param array $input
     * @dataProvider getValidReverseTransformData
     */
    public function testReverseTransformFunctionWithValidData(array $input)
    {
        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField $output */
        $output = $this->customTransformer->reverseTransform($input);

        $this->assertInstanceOf('\Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField', $output);
        $this->assertSame($input['label'], $output->getLabel());
        $this->assertSame($input['type'], $output->getType());

        if (!empty($input['required'])) {
            $options = $output->getOptions();
            $this->assertTrue($options['required']);
        }

        if (isset($input['name'])) {
            $this->assertSame($input['name'], $output->getName());
        }
    }

    /**
     * @return array
     */
    public function getTransformData()
    {
        $output = [
            'name' => 'name',
            'type' => 'type',
            'label' => null,
            'required' => false,
        ];

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField | \PHPUnit_Framework_MockObject_MockObject $field */
        $field = $this->createMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField');
        $field
            ->expects($this->once())
            ->method('getName')
            ->willReturn($output['name'])
        ;

        $field
            ->expects($this->once())
            ->method('getType')
            ->willReturn($output['type'])
        ;

        $field
            ->expects($this->once())
            ->method('getLabel')
            ->willReturn($output['label'])
        ;

        $field
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn(['required' => $output['required']])
        ;

        return [
            'emptyData' => [
                'input' => null,
                'output' => [],
            ],
            'invalidData' => [
                'input' => 'string',
                'output' => [],
            ],
            'validData' => [
                'input' => $field,
                'output' => $output,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidReverseTransformData()
    {
        return [
            'emptyData' => [
                'input' => null,
            ],
            'invalidData' => [
                'input' => 'string',
            ],
            'incompleteDataNoLabel' => [
                'input' => [
                    'type' => 'text',
                    'required' => true,
                ],
            ],
            'incompleteDataNoType' => [
                'input' => [
                    'label' => 'label',
                    'required' => true,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidReverseTransformData()
    {
        return [
            'requiredField' => [
                [
                    'label' => 'label',
                    'type' => 'type',
                    'required' => true,
                ],
            ],
            'fieldWithName' => [
                [
                    'label' => 'label',
                    'type' => 'type',
                    'name' => 'name',
                ],
            ],
        ];
    }
}
