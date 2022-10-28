<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Tests\Mapping\Metadata;

use Integrated\Bundle\SlugBundle\Mapping\Metadata\ClassMetadata;
use Integrated\Bundle\SlugBundle\Mapping\PropertyMetadataInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    /**
     * @var ClassMetadata
     */
    private $metadata;

    protected function setUp(): void
    {
        $this->metadata = new ClassMetadata();
    }

    public function testProperties()
    {
        /** @var PropertyMetadataInterface|MockObject $prop1 */
        $prop1 = $this->getMockBuilder(PropertyMetadataInterface::class)->getMock();

        $prop1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('prop1')
        ;

        /** @var PropertyMetadataInterface|MockObject $prop2 */
        $prop2 = $this->getMockBuilder(PropertyMetadataInterface::class)->getMock();

        $prop2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('prop2')
        ;

        /** @var PropertyMetadataInterface|MockObject $propDuplicate */
        $propDuplicate = $this->getMockBuilder(PropertyMetadataInterface::class)->getMock();

        $propDuplicate
            ->expects($this->once())
            ->method('getName')
            ->willReturn('prop2')
        ;

        $this->metadata->setProperties([$prop1]);

        $this->assertSame(['prop1' => $prop1], $this->metadata->getProperties());

        $this->metadata->addProperty($prop2);
        $this->metadata->addProperty($propDuplicate);

        $this->assertSame(['prop1' => $prop1, 'prop2' => $propDuplicate], $this->metadata->getProperties());
    }
}
