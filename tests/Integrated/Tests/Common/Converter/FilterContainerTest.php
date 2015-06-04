<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Converter;

use Integrated\Common\Converter\FilterContainer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FilterContainerTest extends ContainerTest
{
    public function testAddAndGetInvalidUTF8()
    {
        $container = $this->getInstance();

        $container->add('key', "\xf0\x28\x8c\xbc");
        $container->add('key', "\xf0\x90\x28\xbc");
        $container->add('key', "\xf0\x28\x8c\x28");

        foreach ($container->get('key') as $value) {
            self::assertTrue(mb_check_encoding($value, 'UTF-8'));
        }
    }

    public function testSetAndGetInvalidUTF8()
    {
        $container = $this->getInstance();
        $container->set('key', "\xc3\x28");

        self::assertTrue(mb_check_encoding($container->get('key')[0], 'UTF-8'));

        $container->set('key', "\xa0\xa1");

        self::assertTrue(mb_check_encoding($container->get('key')[0], 'UTF-8'));

        $container->set('key', "\xe2\x28\xa1");

        self::assertTrue(mb_check_encoding($container->get('key')[0], 'UTF-8'));
    }

    /**
     * @return FilterContainer
     */
    protected function getInstance()
    {
        return new FilterContainer();
    }
}
