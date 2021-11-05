<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Command;

use DateTime;
use DateTimeZone;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Solr\Indexer\Job;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerQueueCommand extends Command
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * IndexerQueueCommand constructor.
     *
     * @param DocumentManager   $documentManager
     * @param QueueInterface    $queue
     * @param ResolverInterface $resolver
     */
    public function __construct(DocumentManager $documentManager, QueueInterface $queue, ResolverInterface $resolver)
    {
        parent::__construct();

        $this->documentManager = $documentManager;
        $this->queue = $queue;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('solr:indexer:queue')
            ->addArgument('id', InputArgument::IS_ARRAY, 'One or more content types that need to be indexed')
            ->addOption(
                'full',
                'f',
                InputOption::VALUE_NONE,
                'Do a full index of all the content this will override any given content types'
            )
            ->addOption(
                'delete',
                null,
                InputOption::VALUE_NONE,
                'Delete all the content for the given content types or if none given clear out the whole index'
            )
            ->addOption(
                'commit',
                null,
                InputOption::VALUE_NONE,
                'Queue a commit'
            )
            ->addOption('ignore', 'i', InputOption::VALUE_NONE, 'Ignore content types that do not exist')
            ->setDescription('Queue all the content of the given content type for solr indexing')
            ->setHelp('
The <info>%command.name%</info> command starts a index of the site.

<info>php %command.full_name%</info>
');
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //  validate the content types unless validation is ignored

        if ($input->getArgument('id') && !$input->getOption('ignore')) {
            $code = $this->executeValidation($input, $output);

            if ($code) {
                return (int) $code;
            }
        }

        if ($input->getOption('delete')) {
            return $this->executeDelete($input, $output);
        }

        if ($input->getOption('commit')) {
            $this->doIndexCommit();

            return 0;
        }

        if (!$input->getArgument('id') && !$input->getOption('full')) {
            throw new InvalidArgumentException(
                'You need to give one or more content types or choose the --full or --delete option'
            );
        }

        return $this->executeIndex($input, $output);
    }

    /**
     * validate the ids in de input.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    protected function executeValidation(InputInterface $input, OutputInterface $output)
    {
        $types = [];

        foreach ($this->resolver->getTypes() as $type) {
            $types[$type->getId()] = $type->getId();
        }

        $invalid = [];

        foreach ($input->getArgument('id') as $id) {
            if (!isset($types[$id])) {
                $invalid[] = $id;
            }
        }

        if ($invalid) {
            $text = sprintf('The content types "%s" do not exists', implode(', ', $invalid));

            if ($input->getOption('no-interaction')) {
                throw new InvalidArgumentException($text);
            }

            // ask the user if he/she want to continue or not.

            $output->writeln($text);

            if (!$this->getQuestion()->ask(
                $input,
                $output,
                new ConfirmationQuestion('Would you want to continue? [y/N] ', false)
            )) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * queue a delete on the solr index.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function executeDelete(InputInterface $input, OutputInterface $output)
    {
        $this->doIndexCleanup($input->getArgument('id'));
        $this->doIndexCommit();

        return 0;
    }

    /**
     * queue the indexing of content in to solr.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function executeIndex(InputInterface $input, OutputInterface $output)
    {
        // Don't hydrate for performance reasons
        $builder = $this->documentManager->createQueryBuilder(Content::class);
        $builder->select('id', 'contentType', 'class')->hydrate(false);

        if ($input->getOption('full')) {
            $result = $builder->getQuery()->execute()->immortal();

            // The entire site is going to be reindex so everything that is now in the queue
            // will be redone so just clear it so content is not double indexed.

            $this->queue->clear();
        } else {
            $builder->field('contentType')->in($input->getArgument('id'));

            $result = $builder->getQuery()->execute()->immortal();
        }

        if ($count = $result->count()) {
            $progress = new ProgressBar($output, $count);

            $progress->setRedrawFrequency(min(max(floor($count / 250), 1), 100));
            $progress->setFormat('verbose');

            $progress->start($count);

            // get the current time as it will be required at the end for the solr clean up.

            $date = new DateTime();

            $this->doIndex($result, $progress);
            $this->doIndexCleanup($input->getArgument('id'), $date);
            $this->doIndexCommit();

            $progress->display();
            $progress->finish();
        } else {
            // No content was found but that does not mean nothing should be done. As the solr
            // index should reflect the database so delete all the content for the matching
            // content types out the solr index.

            $this->doIndexCleanup($input->getArgument('id'));
            $this->doIndexCommit();
        }

        $this->documentManager->clear();

        return 0;
    }

    /**
     * Add all the documents in the cursor to the solr queue.
     *
     * @param object      $cursor
     * @param ProgressBar $progress
     */
    protected function doIndex(object $cursor, ProgressBar $progress)
    {
        // the document manager need to be cleared from time to time so this counter keeps
        // track of that.

        $count = 0;

        foreach ($cursor as $document) {
            $progress->advance();

            $job = new Job('ADD');

            $contentType = isset($document['contentType']) ? $document['contentType'] : '';

            $job->setOption('document.id', $contentType.'-'.$document['_id']);

            $job->setOption('document.data', json_encode(['id' => $document['_id']]));
            $job->setOption('document.class', $document['class']);
            $job->setOption('document.format', 'json');

            $this->queue->push($job);

            if (($count++ % 1000) == 0) {
                $this->documentManager->clear();
            }
        }
    }

    /**
     * delete all the types or everything if none is given.
     *
     * @param array    $types
     * @param DateTime $date
     */
    protected function doIndexCleanup(array $types, DateTime $date = null)
    {
        $query = [];

        if ($types) {
            $query[] = 'type_name:("'.implode('" OR "', $types).'")';
        } else {
            $query[] = '*:*';
        }

        if ($date) {
            $date = clone $date;
            $date->setTimezone(new DateTimeZone('UTC'));

            $query[] = '-_time_:['.$date->format('Y-m-d\TG:i:s\Z').' TO *]';
        }

        // Delete everything else that did not got a update or does not exist anymore in the
        // database.

        $job = new Job('DELETE');
        $job->setOption('query', implode(' ', $query));

        $this->queue->push($job, 1);
    }

    /**
     * close up with a commit.
     */
    protected function doIndexCommit()
    {
        $this->queue->push(new Job('COMMIT', ['softcommit' => 'true']), 2);
    }

    /**
     * @return QuestionHelper
     */
    protected function getQuestion()
    {
        return $this->getHelper('question');
    }
}
