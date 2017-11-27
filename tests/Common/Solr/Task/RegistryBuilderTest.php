<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Task;

use Integrated\Common\Solr\Task\RegistryBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testAddHandler()
    {
        $expected = [
            'class1' => [$this, 'testAddHandler'],
            'class2' => [self::class, 'assertTrue'],
            'class3' => 'is_object',
            'class4' => function () {
            },
        ];

        $builder = $this->getInstance();

        $builder->addHandler('CLASS1', $expected['class4']);
        $builder->addHandler('CLASS2', $expected['class3']);
        $builder->addHandler('CLASS3', $expected['class2']);
        $builder->addHandler('CLASS4', $expected['class1']);

        $builder->addHandler('CLASS1', $expected['class1']);
        $builder->addHandler('CLASS2', $expected['class2']);
        $builder->addHandler('CLASS3', $expected['class3']);
        $builder->addHandler('CLASS4', $expected['class4']);

        self::assertSame($expected, $builder->getRegistry()->getHandlers());
    }

    public function testAddHandlers()
    {
        $expected = [
            'class1' => [$this, 'testAddHandler'],
            'class2' => [self::class, 'assertTrue'],
            'class3' => 'is_object',
            'class4' => function () {
            },
        ];

        $builder = $this->getInstance();

        $builder->addHandlers([
            'CLASS1' => $expected['class4'],
            'CLASS2' => $expected['class3'],
            'CLASS3' => $expected['class2'],
        ]);

        $builder->addHandlers([
            'CLASS2' => $expected['class2'],
            'CLASS3' => $expected['class3'],
            'CLASS4' => $expected['class4'],
        ]);

        $builder->addHandlers([
            'CLASS1' => $expected['class1'],
        ]);

        self::assertSame($expected, $builder->getRegistry()->getHandlers());
    }

    /**
     * @return RegistryBuilder
     */
    protected function getInstance()
    {
        return new RegistryBuilder();
    }
}
