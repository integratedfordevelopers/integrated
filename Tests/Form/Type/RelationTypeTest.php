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

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\RelationType;

use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationTypeTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
     * @param array $data
     */
    public function testSubmitValidData(array $data)
    {
        $type = $this->getInstance();
        $form = $this->factory->create($type, new Relation());

        $form->submit($data);
        $this->assertTrue($form->isSynchronized());
        $this->assertInstanceOf('\Integrated\Bundle\ContentBundle\Document\Relation\Relation', $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @return array
     */
    public function getValidTestData()
    {
        return [
            [
                'data' => [
                    'name' => 'Relation with no sources and targets',
                    'type' => 'type',
                    'sources' => [],
                    'targets' => [],
                    'multiple' => false,
                    'required' => true
                ],
                'data' => [
                    'name' => 'Relation with  sources and targets',
                    'type' => 'type',
                    'sources' => [
                        $this->getMock('Integrated\Common\ContentType\ContentTypeInterface'),
                    ],
                    'targets' => [
                        $this->getMock('Integrated\Common\ContentType\ContentTypeInterface'),
                    ],
                ],
            ],

        ];
    }

    /**
     * @return RelationType
     */
    protected function getInstance()
    {
        return new RelationType();
    }
}
