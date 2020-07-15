<?php

namespace Integrated\Bundle\ThemeBundle\Command;

use Integrated\Bundle\ThemeBundle\Scraper\Scraper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScraperRunCommand extends Command
{
    /**
     * @var Scraper
     */
    private $scraper;

    /**
     * ScraperCommand constructor.
     *
     * @param Scraper $scraper
     */
    public function __construct(Scraper $scraper)
    {
        parent::__construct();

        $this->scraper = $scraper;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('scraper:run')
            ->setDescription('Scrape scraper pages');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->scraper->run();
    }
}
