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

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbItem;
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\Channel\ChannelContext;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbResolverTest extends TestCase
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

    protected function setup()
    {
        $this->documentManager = $this->createMock(DocumentManager::class);
        $this->urlResolver = $this->createMock(UrlResolver::class);
        $this->channelContext = $this->createMock(ChannelContext::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->request = $this->createMock(Request::class);
        $this->requestStack->method('getMasterRequest')->willReturn($this->request);

        $this->breadcrumbResolver = new BreadcrumbResolver(
            $this->documentManager,
            $this->urlResolver,
            $this->channelContext,
            $this->requestStack
        );
    }

    public function testGetBreadcrumb()
    {
        $channel = new Channel();
        $channel->setId('my_channel');

        $this->request->method('getPathInfo')->willReturn('/my/page/my-article');
        $this->channelContext->method('getChannel')->willReturn($channel);

        $pageRepository = $this->createMock(ObjectRepository::class);
        $this->documentManager
            ->expects($this->at(0))
            ->method('getRepository')
            ->with(Page::class)->willReturn($pageRepository);

        $contentRepository = $this->createMock(ObjectRepository::class);
        $this->documentManager
            ->expects($this->at(1))
            ->method('getRepository')
            ->with(Content::class)
            ->willReturn($contentRepository);

        $this->urlResolver
            ->expects($this->once())
            ->method('generateUrl')
            ->willReturn('/my');

        $page = new Page();
        $page->setTitle('My page');
        $page->setPath('/my/page');

        $article = new Article();
        $article->setTitle('My article');
        $article->setSlug('my');
        $article->addChannel($channel);
        $article->getPublishTime()->setStartDate(new \DateTime());
        $article->getPublishTime()->setEndDate(new \DateTime('next week'));

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

        $expectedResult = [
            new BreadcrumbItem('My article', '/my'),
            new BreadcrumbItem('My page', '/my/page'),
        ];
        $this->assertEquals($expectedResult, $this->breadcrumbResolver->getBreadcrumb());
    }
}
