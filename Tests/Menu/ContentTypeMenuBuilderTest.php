<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Menu;

use Integrated\Bundle\ContentBundle\Menu\ContentTypeMenuBuilder;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeMenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Knp\Menu\FactoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectRepository;

    /**
     * @var ContentTypeMenuBuilder
     */
    protected $menuBuilder;

    /**
     * Setup the test
     */
    protected function setup()
    {
        $this->factory = $this->getMock('Knp\Menu\FactoryInterface');
        $this->objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->menuBuilder = new ContentTypeMenuBuilder($this->factory, $this->objectRepository);
    }

    /**
     * Test createMenu function without content types
     */
    public function testCreateMenuFunctionWithoutContentTypes()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Test createMenu function with content types
     */
    public function testCreateMenuFunctionWithContentTypes()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    /**
     * Test createMenu function with mulitple categories
     */
    public function testCreateMenuFunctionWithMultipleCategories()
    {
        $this->markTestIncomplete('Not implemented yet');
    }
}
