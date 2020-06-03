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
use Symfony\Component\Cache\Simple\ApcuCache;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class ScraperPageLoader implements LoaderInterface
{
    const CACHEKEY = 'scraper.pagelist';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ApcuAdapter
     */
    private $cache;

    /**
     * @var Array
     */
    private $pageList = null;

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
        $this->cache = new ApcuCache('integrated.theme');
        $this->entityManager = $entityManager;
        $this->channelContext = $channelContext;
    }

    /**
     * @param string $name
     *
     * @return Source
     * @throws LoaderError
     */
    public function getSourceContext($name)
    {
        if (!$channel = $this->channelContext->getChannel()) {
            throw new LoaderError(sprintf('Unkown channel for template "%s"', $name));
        }

        if (!$template = $this->entityManager->getRepository(ScraperEntity::class)->findOneBy(['channelId' => $channel->getId(), 'templateName' => $name])) {
            throw new LoaderError(sprintf('Template "%s" does not exist for channel', $name));
        }

        return new Source($template->getTemplate(), $name);
    }

    public function exists($name)
    {
        if (!$channel = $this->channelContext->getChannel()) {
            return false;
        }

        $this->pageListCacheWarmup();

        if (!isset($this->pageList[$channel->getId()])) {
            return false;
        }

        return in_array($name, $this->pageList[$channel->getId()]);
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        if (!$channel = $this->channelContext->getChannel()) {
            throw new LoaderError(sprintf('Unkown channel for template "%s"', $name));
        }

        if (!$template = $this->entityManager->getRepository(ScraperEntity::class)->findOneBy(['channelId' => $channel->getId(), 'templateName' => $name])) {
            throw new LoaderError(sprintf('Template "%s" does not exist for channel', $name));
        }

        return ($time > $template->getLastModified());
    }

    /**
     * @param bool $force
     *
     * @throws InvalidArgumentException
     */
    public function pageListCacheWarmup(bool $force = false)
    {
        if (!$force && $this->pageList !== null) {
            return;
        }

        if (!$force && $this->pageList = $this->cache->get(self::CACHEKEY)) {
            return;
        }

        $this->pageList = [];

        $scrapers = $this->entityManager->getRepository(ScraperEntity::class)->findAll();
        foreach ($scrapers as $scraper) {
            $this->pageList[$scraper->getChannelId()][] = $scraper->getTemplateName();
        }

        $this->cache->set(self::CACHEKEY, $this->pageList);
    }
}
