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

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Integrated\Bundle\ThemeBundle\Entity\Scraper as ScraperEntity;
use Rct567\DomQuery\DomQuery;
use Symfony\Component\HttpKernel\Kernel;
use Twig\Loader\FilesystemLoader;

class Scraper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var FilesystemLoader
     */
    private $loader;
    /**
     * @var ScraperPageLoader
     */
    private $scraperPageLoader;

    /**
     * Scraper constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param DocumentManager        $documentManager
     * @param Kernel                 $kernel
     * @param FilesystemLoader       $loader
     * @param ScraperPageLoader      $scraperPageLoader
     */
    public function __construct(EntityManagerInterface $entityManager, DocumentManager $documentManager, Kernel $kernel, FilesystemLoader $loader, ScraperPageLoader $scraperPageLoader)
    {
        $this->entityManager = $entityManager;
        $this->documentManager = $documentManager;
        $this->kernel = $kernel;
        $this->loader = $loader;
        $this->scraperPageLoader = $scraperPageLoader;
    }

    public function prepare(ScraperEntity $scraper)
    {

        try {
            $template = file_get_contents($this->kernel->locateResource($scraper->getTemplateName()));

            preg_match_all('/{% block (.*) %}([\s\S]*){% endblock(.*)%}/msU', $template, $matches);

            $blocks = [];
            foreach ($matches[1] as $match) {
                $blocks[] = $match;
            }

            foreach ($scraper->getBlocks() as $block) {
                if (($key = array_search($block->getName(), $blocks)) !== false) {
                    unset($blocks[$key]);
                } else {
                    $scraper->removeBlock($block);
                }
            }

            foreach ($blocks as $block) {
                $blockItem = new ScraperEntity\Block();
                $blockItem->setName($block);
                $blockItem->setMode($blockItem::MODE_IGNORE);

                $this->entityManager->persist($blockItem);

                $scraper->addBlock($blockItem);
            }

            $this->entityManager->flush();
        } catch (\Exception $e) {
            $scraper->setLastError((string) $e);

            $this->entityManager->flush();

            return;
        }

        $this->scraperPageLoader->pageListCacheWarmup(true);

        $this->run($scraper);
    }

    public function run(ScraperEntity $scraper = null)
    {
        if ($scraper === null) {
            $scapers = $this->entityManager->getRepository(ScraperEntity::class)->findAll();
        } else {
            $scapers = [$scraper];
        }

        /** @var ScraperEntity $scraper */
        foreach ($scapers as $scraper) {

            try {
                //$pattern = '/' . $config['delimiter'] . '/';
                $html = $this->replaceUrls(file_get_contents($scraper->getUrl()), $scraper->getUrl());

                $dom = new DomQuery($html);

                $template = file_get_contents($this->kernel->locateResource($scraper->getTemplateName()));

                foreach ($scraper->getBlocks() as $block) {
                    if ($block->getMode() == ScraperEntity\Block::MODE_IGNORE) {
                        continue;
                    }

                    preg_match('/{% block '.$block->getName().' %}([\s\S]*){% endblock(.*)%}/msU', $template, $matches);

                    switch ($block->getMode()) {
                        case ScraperEntity\Block::MODE_REPLACE:
                            $dom->find($block->getSelector())->replaceWith('<scrapermodification>'.$matches[0].'</scrapermodification>');
                            break;

                        case ScraperEntity\Block::MODE_APPEND:
                            $dom->find($block->getSelector())->append('<scrapermodification>'.$matches[0].'</scrapermodification>');
                            break;

                        case ScraperEntity\Block::MODE_REPLACE_INNER:
                            $dom->find($block->getSelector())->html('<scrapermodification>'.$matches[0].'</scrapermodification>');
                            break;
                    }
                }

                $html = (string) $dom;

                $scraper->setTemplate($html);
                $scraper->setLastModified(time());
                $scraper->setLastError();
            } catch (\Exception $e) {
                $scraper->setLastError((string) $e);
            }

            $this->entityManager->flush();
        }
    }

    /**
     * @param string $html
     * @param string $url
     * @return string
     */
    protected function replaceUrls($html, $url)
    {
        $host = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);

        // Replace relative URL's
        $html = preg_replace('/((?:href|src) *= *[\'"](?!(http|mailto|data:|\/\/)))/i', '$1' . $host, $html);

        // Remove base
        $html = preg_replace('|<base href="(.+)"\s?/>|', '', $html);

        return $html;
    }
}
