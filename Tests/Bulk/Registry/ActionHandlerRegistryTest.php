<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Bulk\Registry;

use Integrated\Bundle\ContentBundle\Bulk\ActionHandler\ActionHandlerInterface;
use Integrated\Bundle\ContentBundle\Bulk\Registry\ActionHandlerRegistry;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionHandlerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionHandlerInterface[]
     */
    protected $handlers;

    /**
     * @var ActionHandlerRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->handlers = [
            'class1' => $this->getMockBuilder(ActionHandlerInterface::class)->getMock(),
            'class2' => $this->getMockBuilder(ActionHandlerInterface::class)->getMock()
        ];

        $this->registry = new ActionHandlerRegistry($this->handlers);
    }

    public function testHasHandlerWithValidHandler()
    {
        $this->assertTrue($this->registry->hasHandler('class1'));
    }

    public function testHasHandlerWithInvalidHandler()
    {
        $this->assertFalse($this->registry->hasHandler('UNKNOWN_CLASS'));
    }

    public function testGetHandlerWithValidHandler()
    {
        $this->assertSame($this->handlers['class1'], $this->registry->getHandler('class1'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegistryWithInvalidHandler()
    {
        $this->registry->getHandler('UNKNOWN_CLASS');
    }
}
