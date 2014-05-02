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
use Exception;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Common\Solr\Converter\ConverterInterface;
use Integrated\Common\Solr\Indexer\Job;
use Integrated\Common\Queue\QueueInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerQueueAllCommand extends ContainerAwareCommand
{
	/**
	 * @var DocumentManager
	 */
	private $dm = null;

	/**
	 * @var QueueInterface
	 */
	private $queue = null;

	/**
	 * @var SerializerInterface
	 */
	private $serializer = null;

	/**
	 * @var ConverterInterface
	 */
	private $converter = null;

	/**
	 * @var int
	 */
	private $count;

	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setName('solr:indexer:full-index')
			->setDescription('Queue all the content of the site for solr indexing')
			->setHelp('
The <info>%command.name%</info> command starts a full index of the site.

<info>php %command.full_name%</info>
'
			);
	}

	/**
	 * @see Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// @todo make it flexible so that is really indexes everything

		$result = $this->getDocumentManager()->getUnitOfWork()->getDocumentPersister('Integrated\Bundle\ContentBundle\Document\Content\Content')->loadAll();
        $count = count($result);

		/** @var $progress ProgressHelper */
		$progress = $this->getHelperSet()->get('progress');
		$progress->setRedrawFrequency(min(max(floor($count / 250), 1), 100));
		$progress->setFormat(ProgressHelper::FORMAT_VERBOSE);

		$progress->start($output, $count);

		// get the current time as it will be required at the end for the solr clean up.

		$date = new DateTime();
		$date->setTimezone(new DateTimeZone('UTC'));

		try	{

			$this->count = 0;
			$this->getQueue()->clear();

			foreach($result as $document) {
				$progress->advance();

				$job = new Job('ADD');

				$job->setOption('document.id', $this->getConverter()->getId($document));

				$job->setOption('document.data', $this->getSerializer()->serialize($document, 'json'));
				$job->setOption('document.class', get_class($document));
				$job->setOption('document.format', 'json');

				$this->getQueue()->push($job);

				if ($this->count++ % 1000) { $this->getDocumentManager()->clear(); }
			}

			// delete everything else that did not have a update from the index

			$job = new Job('DELETE');
			$job->setOption('query', '*:* -_time_:[' . $date->format('Y-m-d\TG:i:s\Z') . ' TO *]');

			$this->getQueue()->push($job, 1);

			// close up with a optimize and a commit

			$this->getQueue()->push(new Job('OPTIMIZE'), 2);
			$this->getQueue()->push(new Job('COMMIT'), 3);

		} catch (Exception $e) {
			$output->writeln("Aborting: " . $e->getMessage());

			return 1;
		}

		$this->getDocumentManager()->clear();

		$progress->display();
		$progress->finish();

		return 0;

	}

	/**
	 * @return DocumentManager
	 */
	protected function getDocumentManager()
	{
		if ($this->dm === null) {
			$this->dm = $this->getContainer()->get('doctrine_mongodb.odm.document_manager');
		}

		return $this->dm;
	}

	/**
	 * @return ConverterInterface
	 */
	protected function getConverter()
	{
		if ($this->converter === null) {
			$this->converter = $this->getContainer()->get('integrated_solr.converter');
		}

		return $this->converter;
	}

	/**
	 * @return SerializerInterface
	 */
	protected function getSerializer()
	{
		if ($this->serializer === null) {
			$this->serializer = $this->getContainer()->get('integrated_solr.indexer.serializer');
		}

		return $this->serializer;
	}

	/**
	 * @return QueueInterface
	 */
	protected function getQueue()
	{
		if ($this->queue === null) {
			$this->queue = $this->getContainer()->get('integrated_queue.solr_indexer');
		}

		return $this->queue;
	}
}