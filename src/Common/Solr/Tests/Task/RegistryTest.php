<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Tests\Task;

use Integrated\Common\Solr\Task\Registry;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryTest extends \PHPUnit\Framework\TestCase
{
    public function testHasHandler()
    {
        $handlers = [
            'class1' => function () {
            },
            'class2' => function () {
            },
            'class3' => function () {
            },
        ];

        $registry = $this->getInstance($handlers);

        self::assertTrue($registry->hasHandler('class1'));
        self::assertTrue($registry->hasHandler('class2'));
        self::assertTrue($registry->hasHandler('class3'));
        self::assertFalse($registry->hasHandler('class4'));
    }

    public function testGetHandler()
    {
        $handlers = [
            'class1' => function () {
            },
            'class2' => function () {
            },
            'class3' => function () {
            },
        ];

        $registry = $this->getInstance($handlers);

        self::assertSame($handlers['class1'], $registry->getHandler('class1'));
        self::assertSame($handlers['class2'], $registry->getHandler('class2'));
        self::assertSame($handlers['class3'], $registry->getHandler('class3'));
    }

    public function testGetHandlerNotFound()
    {
        $this->expectException(\Integrated\Common\Solr\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('this-is-a-task-that-does-not-have-a-handler');

        $this->getInstance()->getHandler('this-is-a-task-that-does-not-have-a-handler');
    }

    public function testGetHandlers()
    {
        $handlers = [
            'class1' => function () {
            },
            'class2' => function () {
            },
            'class3' => function () {
            },
        ];

        self::assertSame($handlers, $this->getInstance($handlers)->getHandlers());
    }

    /**
     * @param callable[] $handlers
     *
     * @return Registry
     */
    protected function getInstance(array $handlers = [])
    {
        return new Registry($handlers);
    }
}
