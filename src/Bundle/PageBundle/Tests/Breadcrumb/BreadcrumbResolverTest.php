<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Tests\Breadcrumb;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbItem;
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Test for BreadcrumbResolver.
 */
class BreadcrumbResolverTest extends \PHPUnit\Framework\TestCase
{
    const TEMPLATE = 'default';

    /**
     * @var DocumentManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $documentManager;

    /**
     * @var urlResolver | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlResolver;

    /**
     * @var ChannelContextInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $channelContext;

    /**
     * @var RequestStack | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestStack;

    /**
     * @var Request | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var BreadcrumbResolver
     */
    protected $breadcrumbResolver;

    /**
     * Setup the test.
     */
    protected function setup()
    {
        $this->documentManager = $this->createMock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->urlResolver = $this->createMock('Integrated\Bundle\PageBundle\Services\UrlResolver');
        $this->channelContext = $this->createMock('Integrated\Common\Content\Channel\ChannelContext');
        $this->requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');

        $this->request = $this->createMock('Symfony\Component\HttpFoundation\Request');
        $this->requestStack->method('getMasterRequest')->willReturn($this->request);

        $this->breadcrumbResolver = new BreadcrumbResolver(
            $this->documentManager,
            $this->urlResolver,
            $this->channelContext,
            $this->requestStack
        );
    }

    /**
     * Test getBreadCrumb.
     */
    public function testGetBreadcrumb()
    {
        $channel = new Channel();
        $channel->setId('my_channel');

        $this->request->method('getPathInfo')->willReturn('/my/page/my-article');
        $this->channelContext->method('getChannel')->willReturn($channel);

        $pageRepository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->documentManager
            ->expects($this->at(0))
            ->method('getRepository')
            ->with(Page::class)->willReturn($pageRepository);

        $contentRepository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->documentManager
            ->expects($this->at(1))
            ->method('getRepository')
            ->with(Content::class)
            ->willReturn($contentRepository);

        $page = new Page();
        $page->setTitle('My page');
        $page->setPath('/my/page');

        $article = new Article();
        $page->setTitle('My article');
        $article->setSlug('my-article');

        $pageRepository
            ->expects($this->at(0))
            ->method('findOneBy')
            ->with(['path' => '/', 'channel.$id' => 'my_channel'])
            ->willReturn(null);

        $contentRepository
            ->expects($this->at(0))
            ->method('findOneBy')
            ->with(['slug' => 'my', 'channels.$id' => 'my_channel'])
            ->willReturn($article);

        $pageRepository
            ->expects($this->at(1))
            ->method('findOneBy')
            ->with(['path' => '/my', 'channel.$id' => 'my_channel'])
            ->willReturn(null);

        $contentRepository
            ->expects($this->at(1))
            ->method('findOneBy')
            ->with(['slug' => 'my-article', 'channels.$id' => 'my_channel'])
            ->willReturn(null);

        $pageRepository
            ->expects($this->at(2))
            ->method('findOneBy')
            ->with(['path' => '/my/page', 'channel.$id' => 'my_channel'])
            ->willReturn($page);

        $this->assertContainsOnlyInstancesOf(BreadcrumbItem::class, $this->breadcrumbResolver->getBreadcrumb());
    }
}
