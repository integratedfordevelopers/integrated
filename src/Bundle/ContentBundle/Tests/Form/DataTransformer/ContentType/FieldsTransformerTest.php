<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Form\DataTransformer\ContentType;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType\FieldsTransformer;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FieldsTransformerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FieldsTransformer
     */
    protected $fieldTransformer;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->fieldTransformer = new FieldsTransformer();
    }

    /**
     * Test instanceOf.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->fieldTransformer);
    }

    /**
     * Test transform function with empty data.
     *
     * @param mixed $input
     * @dataProvider getInvalidTransformData
     */
    public function testTransformFunctionWithInvalidData($input)
    {
        $output = ['default' => [], 'custom' => []];
        $this->assertSame($output, $this->fieldTransformer->transform($input));
    }

    /**
     * Test transform function with data.
     *
     * @param array $input
     * @param array $output
     * @dataProvider getValidTransformData
     */
    public function testTransformFunctionWithValidData(array $input, array $output)
    {
        $this->assertEquals($output, $this->fieldTransformer->transform($input));
    }

    /**
     * Test reverseTransform function with invalid data.
     *
     * @param mixed $input
     * @dataProvider getInvalidReverseTransformData
     */
    public function testReverseTransformFunctionWithInvalidData($input)
    {
        $this->assertSame([], $this->fieldTransformer->reverseTransform($input));
    }

    /**
     * Test reverseTransform function with valid data.
     *
     * @param array $input
     * @dataProvider getValidReverseTransformData
     */
    public function testReverseTransformFunctionWithValidData(array $input)
    {
        $this->assertSame(
            array_merge($input['default'], $input['custom']),
            $this->fieldTransformer->reverseTransform($input)
        );
    }

    /**
     * @return array
     */
    public function getInvalidTransformData()
    {
        return [
            'emptyData' => [
                null,
            ],
            'invalidDataString' => [
                'string',
            ],
            'invalidDataStdClass' => [
                $this->createMock('stdClass'),
            ],
            'invalidDataArray' => [
                [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidTransformData()
    {
        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field|\PHPUnit_Framework_MockObject_MockObject $default1 */
        $default1 = $this->createMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field');
        $default1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name')
        ;

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field|\PHPUnit_Framework_MockObject_MockObject $default2 */
        $default2 = $this->createMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field');
        $default2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name2')
        ;

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field|\PHPUnit_Framework_MockObject_MockObject $duplicateDefault */
        $duplicateDefault = $this->createMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field');
        $duplicateDefault
            ->expects($this->once())
            ->method('getName')
            ->willReturn('name')
        ;

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField|\PHPUnit_Framework_MockObject_MockObject $custom1 */
        $custom1 = $this->createMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField');

        /** @var \Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField|\PHPUnit_Framework_MockObject_MockObject $custom2 */
        $custom2 = $this->createMock('Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField');

        return [
            'validData' => [
                'input' => [
                    $default1,
                    $default2,
                    $duplicateDefault,
                    $custom1,
                    $custom2,
                ],
                'output' => [
                    'default' => [
                        'name' => $duplicateDefault,
                        'name2' => $default2,
                    ],
                    'custom' => [
                        $custom1,
                        $custom2,
                    ],
                ],
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
                null,
            ],
            'inValidKeys' => [
                [
                    'inValidKey1' => 'string',
                    'inValidKey2' => [],
                ],
            ],
            'emptyValues' => [
                [
                    'default' => null,
                    'custom' => null,
                ],
            ],
            'inValidValues' => [
                [
                    'default' => 1,
                    'custom' => 'string',
                ],
            ],
            'inValidValuesInArray' => [
                [
                    'default' => [
                        'string',
                    ],
                    'custom' => [
                        [],
                    ],
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
            'onlyDefaultValues' => [
                [
                    'default' => [
                        $this->createMock('Integrated\Common\ContentType\ContentTypeFieldInterface'),
                        $this->createMock('Integrated\Common\ContentType\ContentTypeFieldInterface'),
                    ],
                    'custom' => [],
                ],
            ],
            'onlyCustomValues' => [
                [
                    'default' => [],
                    'custom' => [
                        $this->createMock('Integrated\Common\ContentType\ContentTypeFieldInterface'),
                    ],
                ],
            ],
            'defaultAndCustomValues' => [
                [
                    'default' => [
                        $this->createMock('Integrated\Common\ContentType\ContentTypeFieldInterface'),
                        $this->createMock('Integrated\Common\ContentType\ContentTypeFieldInterface'),
                    ],
                    'custom' => [
                        $this->createMock('Integrated\Common\ContentType\ContentTypeFieldInterface'),
                    ],
                ],
            ],
        ];
    }
}
