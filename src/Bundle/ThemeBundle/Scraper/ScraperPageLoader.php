<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Scraper;

use Doctrine\ORM\EntityManagerInterface;
use Integrated\Bundle\ThemeBundle\Entity\Scraper as ScraperEntity;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class ScraperPageLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $cachekeyPagelist = 'scraper.pagelist';

    /**
     * @var string
     */
    private $cachekeyLastupdate = 'scraper.lastupdate';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var array
     */
    private $pageList = null;

    /**
     * @var int
     */
    private $lastUpdate;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * ScraperPageLoader constructor.
     *
     * @param EntityManagerInterface  $entityManager
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(EntityManagerInterface $entityManager, ChannelContextInterface $channelContext)
    {
        $this->cachekeyPagelist .= '.'.md5(__DIR__);
        $this->cachekeyLastupdate .= '.'.md5(__DIR__);
        $this->cache = new ApcuAdapter('integrated.theme');
        $this->entityManager = $entityManager;
        $this->channelContext = $channelContext;
        $this->pageList = $this->cache->getItem($this->cachekeyPagelist)->get();
        $this->lastUpdate = $this->cache->getItem($this->cachekeyLastupdate)->get();
    }

    /**
     * @param string $name
     *
     * @return Source
     *
     * @throws LoaderError
     */
    public function getSourceContext($name): Source
    {
        if (!$channel = $this->channelContext->getChannel()) {
            throw new LoaderError(sprintf('Unkown channel for template "%s"', $name));
        }

        if (!$template = $this->entityManager->getRepository(ScraperEntity::class)->findOneBy(['channelId' => $channel->getId(), 'templateName' => $name])) {
            throw new LoaderError(sprintf('Template "%s" does not exist for channel', $name));
        }

        return new Source($template->getTemplate(), $name);
    }

    /**
     * @param string $name
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function exists($name): bool
    {
        if (!$channel = $this->channelContext->getChannel()) {
            return false;
        }

        $this->pageListCacheWarmup();

        if (!isset($this->pageList[$channel->getId()])) {
            return false;
        }

        return \in_array($name, $this->pageList[$channel->getId()]);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getCacheKey($name): string
    {
        if (!$channel = $this->channelContext->getChannel()) {
            return $name;
        }

        return $name.$this->lastUpdate.$channel->getId();
    }

    /**
     * @param string $name
     * @param int    $time
     *
     * @return bool
     *
     * @throws LoaderError
     */
    public function isFresh($name, $time): bool
    {
        if (!$channel = $this->channelContext->getChannel()) {
            throw new LoaderError(sprintf('Unkown channel for template "%s"', $name));
        }

        if (!$template = $this->entityManager->getRepository(ScraperEntity::class)->findOneBy(['channelId' => $channel->getId(), 'templateName' => $name])) {
            throw new LoaderError(sprintf('Template "%s" does not exist for channel', $name));
        }

        return $time > $template->getLastModified();
    }

    /**
     * @param bool $force
     *
     * @throws InvalidArgumentException
     */
    public function pageListCacheWarmup(bool $force = false): void
    {
        if (!$force && $this->pageList !== null && $this->lastUpdate > (time() - 900)) {
            return;
        }

        $this->pageList = [];

        $scrapers = $this->entityManager->getRepository(ScraperEntity::class)->findAll();
        foreach ($scrapers as $scraper) {
            $this->pageList[$scraper->getChannelId()][] = $scraper->getTemplateName();
        }

        $this->lastUpdate = time();

        $this->cache->save($this->cache->getItem($this->cachekeyPagelist)->set($this->pageList));
        $this->cache->save($this->cache->getItem($this->cachekeyLastupdate)->set($this->lastUpdate));
    }
}
