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

use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Menu\ContentTypeMenuBuilder;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeMenuBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FactoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var ContentTypeManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeManager;

    /**
     * @var AuthorizationCheckerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationChecker;

    /**
     * Setup the test.
     */
    protected function setup()
    {
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->contentTypeManager = $this->getMockBuilder(ContentTypeManager::class)->disableOriginalConstructor()->getMock();
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
    }

    /**
     * Test createMenu function with invalid content types.
     */
    public function testCreateMenuFunctionWithInvalidContentType()
    {
        $builder = $this->getInstance();

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with('root')
            ->willReturn($menu)
        ;

        $this->contentTypeManager
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$this->createMock('\stdClass')])
        ;

        $this->assertSame($menu, $builder->createMenu());
    }

    /**
     * Test createMenu function with item without parent.
     */
    public function testCreateMenuFunctionWithItemWithoutParent()
    {
        $builder = $this->getInstance();

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with('root')
            ->willReturn($menu)
        ;

        $this->contentTypeManager
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($this->getItemWithoutParent())
        ;

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $child */
        $child = $this->createMock('Knp\Menu\ItemInterface');

        $child
            ->expects($this->once())
            ->method('addChild')
        ;

        $menu
            ->expects($this->once())
            ->method('addChild')
            ->with('ItemWithoutParent')
            ->willReturn($child)
        ;

        $this->assertSame($menu, $builder->createMenu());
    }

    /**
     * Test createMenu function with items.
     */
    public function testCreateMenuFunctionWithItems()
    {
        $builder = $this->getInstance();

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with('root')
            ->willReturn($menu)
        ;

        $this->contentTypeManager
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($this->getItems())
        ;

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $child1 */
        $child1 = $this->createMock('Knp\Menu\ItemInterface');

        $child1
            ->expects($this->exactly(2))
            ->method('addChild')
        ;

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $child2 */
        $child2 = $this->createMock('Knp\Menu\ItemInterface');

        $child2
            ->expects($this->once())
            ->method('addChild')
        ;

        $menu
            ->expects($this->exactly(2))
            ->method('addChild')
            ->withConsecutive(
                ['ParentWithMultipleLevels'],
                ['ParentWithOneLevel']
            )
            ->willReturnOnConsecutiveCalls($child1, $child2)
        ;

        $this->assertSame($menu, $builder->createMenu());
    }

    /**
     * Test createMenu function with access check.
     */
    public function testCreateMenuFunctionWithItemsWithAccessCheck()
    {
        $builder = $this->getInstance(true);

        $items = $this->getItems();

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with('root')
            ->willReturn($menu)
        ;

        $this->contentTypeManager
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($items)
        ;

        $this->authorizationChecker
            ->expects($this->exactly(3))
            ->method('hasAccess')
            ->willReturnOnConsecutiveCalls(
                true,
                false,
                false
            )
        ;

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $child1 */
        $child1 = $this->createMock('Knp\Menu\ItemInterface');

        $child1
            ->expects($this->once())
            ->method('addChild')
        ;

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $child2 */
        $child2 = $this->createMock('Knp\Menu\ItemInterface');

        $child2
            ->expects($this->never())
            ->method('addChild')
        ;

        $menu
            ->expects($this->exactly(2))
            ->method('addChild')
            ->withConsecutive(
                ['ParentWithMultipleLevels'],
                ['ParentWithOneLevel']
            )
            ->willReturnOnConsecutiveCalls($child1, $child2)
        ;

        $this->assertSame($menu, $builder->createMenu());
    }

    protected function getItemWithoutParent()
    {
        $contentType = $this->createMock('\Integrated\Bundle\ContentBundle\Document\ContentType\ContentType');
        $contentType
            ->expects($this->once())
            ->method('getClass')
            ->willReturn('Integrated\Bundle\ContentBundle\Tests\Menu\FakeContent\ItemWithoutParent')
        ;

        return [$contentType];
    }

    protected function getItems()
    {
        $contentType1 = $this->createMock('\Integrated\Bundle\ContentBundle\Document\ContentType\ContentType');
        $contentType1
            ->expects($this->once())
            ->method('getClass')
            ->willReturn('Integrated\Bundle\ContentBundle\Tests\Menu\FakeContent\ParentWithOneLevel\Item')
        ;

        $contentType2 = $this->createMock('\Integrated\Bundle\ContentBundle\Document\ContentType\ContentType');
        $contentType2
            ->expects($this->once())
            ->method('getClass')
            ->willReturn('Integrated\Bundle\ContentBundle\Tests\Menu\FakeContent\ParentWithMultipleLevels\AbstractItemA\ItemA')
        ;

        $contentType3 = $this->createMock('\Integrated\Bundle\ContentBundle\Document\ContentType\ContentType');
        $contentType3
            ->expects($this->once())
            ->method('getClass')
            ->willReturn('Integrated\Bundle\ContentBundle\Tests\Menu\FakeContent\ParentWithMultipleLevels\ItemB')
        ;

        return [$contentType1, $contentType2, $contentType3];
    }

    /**
     * @param bool $withFilter
     *
     * @return ContentTypeMenuBuilder
     */
    protected function getInstance($withFilter = false)
    {
        return new ContentTypeMenuBuilder(
            $this->factory,
            $this->contentTypeManager,
            $withFilter ? $this->authorizationChecker : null
        );
    }
}
