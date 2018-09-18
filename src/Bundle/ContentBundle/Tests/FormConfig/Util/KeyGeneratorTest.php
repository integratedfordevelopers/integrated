<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\FormConfig\Util;

use Integrated\Bundle\ContentBundle\FormConfig\Util\KeyGenerator;

class KeyGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider generateProvider
     */
    public function testGenerate(string $name, string $expected)
    {
        $this->assertEquals($expected, (new KeyGenerator())->generate($name));
    }

    public function generateProvider()
    {
        return [
            ['ABCDEF', 'abcdef'],
            ['ABCDEF  ABCDEF', 'abcdef_abcdef'],
            ['!@#$%^&*()', '_'],
            ['!@#$%^&*()ABCDEF!@#$%^&*()', '_abcdef_'],
            ['___', '_'],
            ['ABCDEF12345', 'abcdef12345'],
            ['a-b-c-d-e-f!!1-2-3-4-5-6', 'a_b_c_d_e_f_1_2_3_4_5_6'],
            ['abcdef', 'abcdef'],
            [' # ab - cd - ef # ', '_ab_cd_ef_'],
        ];
    }
}
