<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Integrated\Bundle\ContentHistoryBundle\History\Cleaner;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends Command
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var Cleaner
     */
    private $cleaner;

    /**
     * @param DocumentManager $documentManager
     * @param Cleaner         $cleaner
     */
    public function __construct(DocumentManager $documentManager, Cleaner $cleaner)
    {
        parent::__construct();

        $this->documentManager = $documentManager;
        $this->cleaner = $cleaner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('integrated:content-history:clean')
            ->setDescription('Clean content history using configuration')
            ->addOption('clean', 'c', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'Clean fields from class, use: classFQN:fieldname')
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clean = $input->getOption('clean');
        $cleanTable = [];
        foreach ($clean as $value) {
            list($className, $field) = explode(':', $value.':');

            if (!$className) {
                throw new \Exception('Classname not specified');
            }

            if (!$field) {
                throw new \Exception('Field not specified');
            }

            if (!class_exists($className)) {
                $output->writeln(sprintf('Warning: class %s does not seem to exist', $className));
            }

            $cleanTable[$className][] = $field;
        }

        $this->cleaner->setCleanTable($cleanTable);

        // Don't hydrate for performance reasons
        $builder = $this->documentManager->createQueryBuilder(ContentHistory::class);
        $builder->select('id', 'contentClass', 'changeSet', 'action')->hydrate(false);

        $result = $builder->getQuery()->execute()->immortal();

        $progress = new ProgressBar($output, $result->count());

        $progress->setRedrawFrequency(min(max(floor($result->count() / 250), 1), 100));
        $progress->setFormat('verbose');

        $progress->start($result->count());

        $count = 0;

        foreach ($result as $document) {
            $progress->advance();

            $this->cleaner->clean($document);

            if (($count++ % 1000) == 0) {
                $this->documentManager->clear();
            }
        }

        $progress->display();
        $progress->finish();

        $this->documentManager->clear();

        return 0;
    }
}
