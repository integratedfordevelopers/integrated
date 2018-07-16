<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Tests\Connector\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\SocialBundle\Connector\Twitter\Exporter;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;
use Integrated\Common\Channel\Connector\ExporterInterface;
use Integrated\Common\Channel\Exporter\ExporterReponse;
use Integrated\Common\Channel\Tests\Exporter\Mock\NonContentDocument;

class ExporterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TwitterOAuth | \PHPUnit_Framework_MockObject_MockObject
     */
    private $twitter;

    /**
     * @var ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    protected function setUp()
    {
        $this->twitter = $this->createMock(TwitterOAuth::class);
        $this->config = $this->createMock(ConfigInterface::class);
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
        $exporter->export($document, ExporterInterface::STATE_ADD, $channel);

        $this->assertNotInstanceOf(Article::class, $document);

        $this->assertNotInstanceOf(ExporterReponse::class, $document);
    }

    public function testExportWithOtherState()
    {
        $document = new Article();
        $channel = $this->getChannel('channel');

        $exporter = $this->getInstance();
        $exporter->export($document, ExporterInterface::STATE_DELETE, $channel);

        $this->assertNotInstanceOf(ExporterReponse::class, $document);
    }

    public function testExportDoublePosting()
    {
        $document = $this->getArticle();
        $document
            ->method('hasConnector')
            ->willReturn(true);

        $channel = $this->getChannel('channel');

        $exporter = $this->getInstance();
        $exporter->export($document, ExporterInterface::STATE_ADD, $channel);

        $this->assertNotInstanceOf(ExporterReponse::class, $document);
    }

    protected function getInstance(): Exporter
    {
        return new Exporter($this->twitter, $this->config);
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

    protected function getArticle()
    {
        return $this->createMock(Article::class);
    }

    /**
     * @return OptionsInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOptions()
    {
        return $this->createMock(OptionsInterface::class);
    }
}
