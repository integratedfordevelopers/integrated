<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Tests\Mapping\Annotations;

use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use PHPUnit\Framework\TestCase;

class SlugTest extends TestCase
{
    /**
     * @dataProvider slugProvider
     */
    public function testConstructorWithValidData($data)
    {
        $slug = new Slug($data);

        $this->assertEquals($data['fields'], $slug->getFields());
        $this->assertEquals($data['separator'] ?? '-', $slug->getSeparator());
        $this->assertEquals($data['lengthLimit'] ?? 200, $slug->getLengthLimit());
    }

    /**
     * Test the constructor with invalid data.
     */
    public function testConstructorWithInvalidData()
    {
        $this->expectException(\BadMethodCallException::class);

        new Slug(['henk' => 'de vries']);
    }

    public function slugProvider(): array
    {
        return [
            [['fields' => ['id'], 'separator' => '_', 'lengthLimit' => 100]],
            [['fields' => ['id', 'name'], 'lengthLimit' => 100]],
            [['fields' => ['name'], 'separator' => '_']],
        ];
    }
}
