<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Form\Event;

use Integrated\Common\Content\Form\Event\OptionsEvent;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class OptionsEventTest extends FormEventTest
{
    /**
     * @var OptionsResolver | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    protected function setUp()
    {
        parent::setUp();

        $this->resolver = $this->getMock('Symfony\\Component\\OptionsResolver\\OptionsResolver');
    }

    public function testGetResolver()
    {
        self::assertSame($this->resolver, $this->getInstance()->getResolver());
    }

    /**
     * @return OptionsEvent
     */
    protected function getInstance()
    {
        return new OptionsEvent($this->type, $this->metadata, $this->resolver);
    }
}
