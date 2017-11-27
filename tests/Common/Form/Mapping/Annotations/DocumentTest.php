<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Form\Mapping\Annotations;

use Integrated\Common\Form\Mapping\Annotations\Document;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DocumentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the constructor with valid data.
     */
    public function testConstructorWithValidData()
    {
        $document = new Document(['name' => 'Henk de Vries']);
        $this->assertEquals('Henk de Vries', $document->getName());
    }

    /**
     * Test the constructor with invalid data.
     */
    public function testConstructorWithInvalidData()
    {
        $this->expectException(\BadMethodCallException::class);

        new Document(['henk' => 'de vries']);
    }

    /**
     * Test the constructor with a value in data.
     */
    public function testConstructorWithValueToName()
    {
        $document = new Document(['value' => 'Henk de Vries']);
        $this->assertEquals('Henk de Vries', $document->getName());
    }
}
