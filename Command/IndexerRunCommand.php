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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\DBAL\Schema\SchemaException;

use Integrated\Common\Solr\Indexer\IndexerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerRunCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setName('solr:indexer:run')
			->setDescription('Execute a sol indexer run')
			->setHelp('
The <info>%command.name%</info> command starts a indexer run.

<info>php %command.full_name%</info>
'
			);
	}

	/**
	 * @see Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getContainer();

		try	{
			/** @var IndexerInterface $indexer */
			$indexer = $container->get('integrated_solr.indexer');
			$indexer->execute();
		} catch (SchemaException $e) {
			$output->writeln("Aborting: " . $e->getMessage());

			return 1;
		}

		return 0;
	}
}