<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

use Exception;
use Integrated\Common\Converter\ConverterInterface;
use Integrated\Common\Solr\Exception\ConverterException;
use Integrated\Common\Solr\Exception\OutOfBoundsException;
use Integrated\Common\Solr\Exception\SerializerException;
use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Command\Delete;
use Solarium\QueryType\Update\Query\Command\Optimize;
use Solarium\QueryType\Update\Query\Command\Rollback;
use Solarium\QueryType\Update\Query\Document;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * CommandFactory constructor.
     *
     * @param ConverterInterface  $converter
     * @param SerializerInterface $serializer
     */
    public function __construct(ConverterInterface $converter, SerializerInterface $serializer)
    {
        $this->converter = $converter;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function create(JobInterface $job)
    {
        if (!$job->hasAction()) {
            throw new OutOfBoundsException(sprintf('The jobs action is empty, valid actions are "%s"', 'ADD, DELETE, OPTIMIZE, ROLLBACK or COMMIT'));
        }

        // Action specifies which command class to use where the options got
        // optional argument. Except for the ADD and DELETE command. The ADD
        // command need to deserialize a document and for that need the options
        // "document.data", "document.class" and "document.format". And the
        // DELETE command needs a "id" or a "query" but both are also allowed.
        // if those requirements are not met then no command will be created
        // and the result will be null.
        //
        // Options that are not used are ignored and will not generated any
        // kind of error.

        switch (strtoupper($job->getAction())) {
            case 'ADD':
                $command = $this->createAdd($job);

                if (!$command) {
                    $command = $this->createDelete($job);
                }

                return $command;

            case 'DELETE':
                return $this->createDelete($job);

            case 'OPTIMIZE':
                return $this->createOptimize($job);

            case 'COMMIT':
                return $this->createCommit($job);

            case 'ROLLBACK':
                return $this->createRollback($job);
        }

        throw new OutOfBoundsException(sprintf('The jobs action "%s" does not exist, valid actions are "%s"', $job->getAction(), 'ADD, DELETE, OPTIMIZE, ROLLBACK or COMMIT'));
    }

    /**
     * Create a solarium add command.
     *
     * @param JobInterface $job
     *
     * @return Add
     */
    protected function createAdd(JobInterface $job)
    {
        $required = [
            'document.data',
            'document.class',
            'document.format',
        ];

        foreach ($required as $option) {
            if (!$job->hasOption($option)) {
                return null;
            }
        }

        try {
            $document = $this->serializer->deserialize(
                $job->getOption('document.data'),
                $job->getOption('document.class'),
                $job->getOption('document.format')
            );
        } catch (Exception $e) {
            throw new SerializerException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            $document = $this->converter->convert($document);
        } catch (Exception $e) {
            throw new ConverterException($e->getMessage(), $e->getCode(), $e);
        }

        if (!$document->count()) {
            return null;
        }

        $command = new Add();
        $command->addDocument(new Document($document->toArray()));

        if ($job->hasOption('overwrite')) {
            $command->setOverwrite((bool) $job->getOption('overwrite'));
        }
        if ($job->hasOption('commitwithin')) {
            $command->setCommitWithin((bool) $job->getOption('commitwithin'));
        }

        return $command;
    }

    /**
     * Create a solarium delete command.
     *
     * @param JobInterface $job
     *
     * @return Delete
     */
    protected function createDelete(JobInterface $job)
    {
        if (!($job->hasOption('document.id') || $job->hasOption('id') || $job->hasOption('query'))) {
            return null;
        }

        $command = new Delete();

        if ($job->hasOption('document.id')) {
            $command->addId($job->getOption('document.id'));
        }

        if ($job->hasOption('id')) {
            $command->addId($job->getOption('id'));
        }

        if ($job->hasOption('query')) {
            $command->addQuery($job->getOption('query'));
        }

        return $command;
    }

    /**
     * Create a solarium optimize command.
     *
     * @param JobInterface $job
     *
     * @return Optimize
     */
    protected function createOptimize(JobInterface $job)
    {
        $command = new Optimize();

        if ($job->hasOption('maxsegments')) {
            $command->setMaxSegments((bool) $job->getOption('maxsegments'));
        }

        if ($job->hasOption('waitsearcher')) {
            $command->setWaitSearcher((bool) $job->getOption('waitsearcher'));
        }

        if ($job->hasOption('softcommit')) {
            $command->setSoftCommit((bool) $job->getOption('softcommit'));
        }

        return $command;
    }

    /**
     * Create a solarium commit command.
     *
     * @param JobInterface $job
     *
     * @return Commit
     */
    protected function createCommit(JobInterface $job)
    {
        $command = new Commit();

        if ($job->hasOption('waitsearcher')) {
            $command->setWaitSearcher((bool) $job->getOption('waitsearcher'));
        }

        if ($job->hasOption('softcommit')) {
            $command->setSoftCommit((bool) $job->getOption('softcommit'));
        }

        if ($job->hasOption('expungedeletes')) {
            $command->setExpungeDeletes((bool) $job->getOption('expungedeletes'));
        }

        return $command;
    }

    /**
     * Create a solarium rollback command.
     *
     * @param JobInterface $job
     *
     * @return Rollback
     */
    protected function createRollback(JobInterface $job)
    {
        return new Rollback();
    }
}
