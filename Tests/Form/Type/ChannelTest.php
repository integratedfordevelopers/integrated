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

use Integrated\Bundle\ContentBundle\Form\Type\Channel;
use Integrated\Bundle\ContentBundle\Form\Type\CsvArray;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;

class ChannelTest extends TypeTestCase
{
    /**
     * @return array
     */
    protected function getExtensions()
    {
        $childType = new CsvArray();
        return array(new PreloadedExtension(array(
            $childType->getName() => $childType,
        ), array()));
    }

    /**
     * @dataProvider getValidTestData
     * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
     */
    public function testSubmitValidData($data)
    {
        $type = new Channel();
        $form = $this->factory->create($type, new \Integrated\Bundle\ContentBundle\Document\Channel\Channel());

        $form->submit($data);
        $this->assertTrue($form->isSynchronized());
        $this->assertInstanceOf('\Integrated\Bundle\ContentBundle\Document\Channel\Channel', $form->getData());

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
        return array(
            array(
                'data' => array(
                    'name' => 'Channel name',
                    'domains' => 'domain1, domain2'
                )
            ),
            array(
                'data' => array(
                    'name' => 'Channel without domains',
                )
            )

        );
    }
}
