<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Tests\Form\Event;

use Integrated\Common\Content\Form\Event\FormEvent;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormEventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $type;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    protected function setUp(): void
    {
        $this->type = $this->createMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
        $this->metadata = $this->createMock('Integrated\\Common\\Form\\Mapping\\MetadataInterface');
    }

    public function testInterface()
    {
        $event = $this->getInstance();

        self::assertInstanceOf(Event::class, $event);
        self::assertInstanceOf('Integrated\Common\Content\Form\Event\FormEvent', $event);
    }

    public function testGetContentType()
    {
        self::assertSame($this->type, $this->getInstance()->getContentType());
    }

    public function testGetMetadata()
    {
        self::assertSame($this->metadata, $this->getInstance()->getMetadata());
    }

    /**
     * @return FormEvent
     */
    protected function getInstance()
    {
        return new FormEvent($this->type, $this->metadata);
    }
}
