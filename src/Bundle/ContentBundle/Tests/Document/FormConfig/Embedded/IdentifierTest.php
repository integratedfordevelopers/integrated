<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\FormConfig\Embedded;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Identifier;
use Integrated\Common\FormConfig\FormConfigIdentifierInterface;

class IdentifierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Identifier
     */
    private $identifier;

    protected function setUp()
    {
        $this->identifier = new Identifier('content_type', 'key');
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigIdentifierInterface::class, $this->identifier);
    }

    public function testGetContentType()
    {
        $this->assertEquals('content_type', $this->identifier->getContentType());
    }

    public function testGetKey()
    {
        $this->assertEquals('key', $this->identifier->getKey());
    }

    public function testToArray()
    {
        $this->assertEquals([
            'type' => 'content_type',
            'key' => 'key',
        ], $this->identifier->toArray());
    }
}
