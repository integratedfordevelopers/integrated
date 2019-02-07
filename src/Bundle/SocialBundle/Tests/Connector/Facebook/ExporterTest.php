<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Tests\Connector\Facebook;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphNode;
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface;
use Integrated\Bundle\ChannelBundle\Model\Options;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Bundle\SocialBundle\Connector\Facebook\Exporter;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;
use Integrated\Common\Channel\Connector\ExporterInterface;
use Integrated\Common\Channel\Exception\UnexpectedTypeException;
use Integrated\Common\Channel\Exporter\ExporterResponse;
use Integrated\Common\Channel\Tests\Exporter\Mock\NonContentDocument;
use Integrated\Common\Content\ContentInterface;

class ExporterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Facebook | \PHPUnit_Framework_MockObject_MockObject
     */
    private $facebook;

    /**
     * @var ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var UrlResolver | \PHPUnit_Framework_MockObject_MockObject
     */
    private $urlResolver;

    protected function setUp()
    {
        $this->facebook = $this->createMock(Facebook::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->urlResolver = $this->createMock(UrlResolver::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(ExporterInterface::class, $this->getInstance());
    }

    public function testExportNonArticleDocument()
    {
        $document = new NonContentDocument();
        $channel = $this->getChannel('channel');

        $exporter = $this->getInstance();
        $response = $exporter->export($document, ExporterInterface::STATE_ADD, $channel);

        $this->assertNotInstanceOf(ContentInterface::class, $document);

        $this->assertNotInstanceOf(ExporterResponse::class, $response);
    }

    public function testExportWithOtherState()
    {
        $document = new Article();
        $channel = $this->getChannel('channel');

        $exporter = $this->getInstance();
        $response = $exporter->export($document, ExporterInterface::STATE_DELETE, $channel);

        $this->assertNotInstanceOf(ExporterResponse::class, $response);
    }

    public function testExportDoublePosting()
    {
        $document = $this->getArticle();
        $document
            ->method('hasConnector')
            ->willReturn(true);

        $channel = $this->getChannel('channel');

        $exporter = $this->getInstance();
        $response = $exporter->export($document, ExporterInterface::STATE_ADD, $channel);

        $this->assertNotInstanceOf(ExporterResponse::class, $response);
    }

    public function testExportPostResponseInstanceOfFacebookResponseException()
    {
        $document = $this->getArticle();
        $document
            ->method('hasConnector')
            ->willReturn(false);

        $this->config->method('getId')
            ->willReturn(1);

        $options = new Options();
        $options->set('token', 'token-value');

        $this->config->method('getOptions')
            ->willReturn($options);

        $facebookResponse = $this->getFacebookResponse();
        $this->facebook
            ->method('post')
            ->willThrowException(new FacebookResponseException($facebookResponse));

        $exporter = $this->getInstance();
        $response = $exporter->export($document, ExporterInterface::STATE_ADD, $this->getChannel('channel'));

        $this->assertNull($response);
    }

    public function testExportPostResponseInstanceOfGraphNodeException()
    {
        $document = $this->getArticle();
        $document
            ->method('hasConnector')
            ->willReturn(false);

        $this->config->method('getId')
            ->willReturn(1);

        $options = new Options();
        $options->set('token', 'token-value');

        $this->config->method('getOptions')
            ->willReturn($options);

        $facebookResponse = $this->getFacebookResponse();

        $facebookResponse->method('getGraphNode')
            ->willReturn(new \stdClass());

        $this->facebook
            ->method('post')
            ->willReturn($facebookResponse);

        $this->expectException(UnexpectedTypeException::class);

        $exporter = $this->getInstance();
        $exporter->export($document, ExporterInterface::STATE_ADD, $this->getChannel('channel'));
    }

    public function testExportPostResponseInstanceOfGraphNode()
    {
        $document = $this->getArticle();
        $document
            ->method('hasConnector')
            ->willReturn(false);

        $channel = $this->getChannel('channel');

        $configId = 1;
        $this->config->method('getId')
            ->willReturn($configId);

        $configAdapter = 'adapter-facebook';
        $this->config->method('getAdapter')
            ->willReturn($configAdapter);

        $options = new Options();
        $options->set('token', 'token-value');

        $this->config->method('getOptions')
            ->willReturn($options);

        $graphNodeArray = ['id' => 'this-is-the-id'];

        $graphNode = $this->createMock(GraphNode::class);
        $graphNode->method('offsetGet')
            ->will($this->returnCallback(
                function ($key) use ($graphNodeArray) {
                    return $graphNodeArray[$key];
                }
            ));

        $facebookResponse = $this->getFacebookResponse();
        $facebookResponse->method('getGraphNode')
            ->willReturn($graphNode);

        $this->facebook
            ->method('post')
            ->willReturn($facebookResponse);

        $exporter = $this->getInstance();
        $response = $exporter->export($document, ExporterInterface::STATE_ADD, $channel);

        $this->assertInstanceOf(GraphNode::class, $graphNode);

        $this->assertInstanceOf(ExporterResponse::class, $response);

        $this->assertEquals($configId, $response->getConfigId());
        $this->assertEquals($configAdapter, $response->getConfigAdapter());
        $this->assertEquals($graphNodeArray['id'], $response->getExternalId());
    }

    /**
     * @return Exporter
     */
    protected function getInstance(): Exporter
    {
        return new Exporter($this->facebook, $this->config, $this->urlResolver);
    }

    /**
     * @param string $id
     *
     * @return ChannelInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChannel($id)
    {
        $mock = $this->createMock(ChannelInterface::class);
        $mock->method('getId')
            ->willReturn($id);

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getArticle()
    {
        return $this->createMock(Article::class);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFacebookResponse()
    {
        return $this->createMock(FacebookResponse::class);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getOptions()
    {
        return $this->createMock(OptionsInterface::class);
    }
}
