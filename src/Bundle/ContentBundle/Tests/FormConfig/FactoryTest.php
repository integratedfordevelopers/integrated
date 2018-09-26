<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\FormConfig;

use Integrated\Bundle\ContentBundle\Document\FormConfig\FormConfig;
use Integrated\Bundle\ContentBundle\FormConfig\Factory;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigFactoryInterface::class, new Factory());
    }

    public function testCreate()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('content-type');

        $config = (new Factory())->create($type, 'key');

        $this->assertInstanceOf(FormConfig::class, $config);
        $this->assertEquals('content-type', $config->getId()->getContentType());
        $this->assertEquals('key', $config->getId()->getKey());
    }
}
