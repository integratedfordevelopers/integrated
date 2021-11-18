<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Service\StateManager;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexCommand extends Command
{
    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ObjectRepository
     */
    private $workflowRepository;

    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * IndexCommand constructor.
     *
     * @param StateManager $stateManager
     */
    public function __construct(StateManager $stateManager, ResolverInterface $resolver, ObjectRepository $workflowRepository, LockFactory $lockFactory)
    {
        $this->stateManager = $stateManager;
        $this->resolver = $resolver;
        $this->workflowRepository = $workflowRepository;
        $this->lockFactory = $lockFactory;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('workflow:index')

            ->addArgument('id', InputArgument::IS_ARRAY, 'One or more workflow ids that need to be indexed')

            ->addOption('full', 'f', InputOption::VALUE_NONE, 'Do a full index of all the workflow, this will override any given workflow ids')
            ->addOption('ignore', 'i', InputOption::VALUE_NONE, 'Ignore workflow ids that do not exist')

            ->setDescription('Queue the solr indexing of content items of one or more workflow\'s')
            ->setHelp('
The <info>%command.name%</info> command starts a index of all the content from the given workflow.

<info>php %command.full_name%</info>
');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getArgument('id') && !$input->getOption('full')) {
            throw new InvalidArgumentException('You need to give one or more workflow ids or choose the --full option');
        }

        $lock = $this->getLock();
        $lock->acquire(true);

        try {
            // get a list of the workflow ids of which the content needs to be (re)indexed

            $workflow = [];

            if ($input->getOption('full')) {
                foreach ($this->findDefinition() as $row) {
                    $workflow[$row->getId()] = $row->getId();
                }
            } else {
                foreach ($this->findDefinition($input->getArgument('id')) as $row) {
                    $workflow[$row->getId()] = $row->getId();
                }

                //  validate the ids unless validation is ignored

                if (!$input->getOption('ignore')) {
                    $invalid = [];

                    foreach ($input->getArgument('id') as $id) {
                        if (!isset($workflow[$id])) {
                            $invalid[] = $id;
                        }
                    }

                    if ($invalid) {
                        throw new InvalidArgumentException(sprintf(
                            'The workflow ids "%s" do not exists',
                            implode(', ', $invalid)
                        ));
                    }
                }
            }

            // Get the content types that belong to the selected workflow ids and issue a index
            // for those types.

            $types = [];

            foreach ($this->findTypes($workflow) as $row) {
                $this->stateManager->ensureWorkflowState($row->getId());
                $types[] = $row->getId();
            }

            if (!$types) {
                return 0;
            }

            $command = null;

            try {
                $command = $this->getApplication()->find('solr:indexer:queue');
            } catch (Exception $e) {
                throw new RuntimeException(sprintf('Could not find the command "%s"', 'solr:indexer:queue'));
            }

            try {
                return $command->run(new ArrayInput(['--ignore' => true, 'id' => $types]), $output);
            } catch (Exception $e) {
                throw new RuntimeException(sprintf(
                    'An error occurred when executing the command "%s"',
                    'solr:indexer:queue'
                ), 0, $e);
            }
        } finally {
            $lock->release();
        }

        return 0;
    }

    /**
     * @param string[] $ids
     *
     * @return Definition[]
     */
    protected function findDefinition(array $ids = null)
    {
        if (null === $ids) {
            return $this->workflowRepository->findAll();
        }

        return $this->workflowRepository->findBy(['id' => $ids]);
    }

    /**
     * @param string[] $ids
     *
     * @return ContentTypeInterface[]
     */
    protected function findTypes(array $ids)
    {
        $result = [];

        foreach ($this->resolver->getTypes() as $type) {
            if ($type->hasOption('workflow') && isset($ids[$type->getOption('workflow')])) {
                $result[] = $type;
            }
        }

        return $result;
    }

    /**
     * @return LockFactory
     */
    protected function getLock()
    {
        return $this->lockFactory->createLock(self::class.md5(__DIR__.$this->getName()));
    }
}
