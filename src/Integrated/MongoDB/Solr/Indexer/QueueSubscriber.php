<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\Solr\Indexer;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\Common\EventSubscriber;

use Integrated\Common\Queue\QueueAwareInterface;

use Integrated\Common\Solr\Converter\ConverterAwareInterface;
use Integrated\Common\Solr\Converter\ConverterInterface;
use Integrated\Common\Solr\Indexer\Job;

use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Integrated\Common\Queue\QueueInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueSubscriber implements EventSubscriber, QueueAwareInterface, SerializerAwareInterface, ConverterAwareInterface
{
	/**
	 * @var QueueInterface
	 */
	private $queue;

	/**
	 * @var SerializerInterface
	 */
	private $serializer;

	/**
	 * @var string
	 */
	private $format = null;

	/**
	 * @var ConverterInterface
	 */
	private $converter;

	/**
	 * @param QueueInterface $queue
	 * @param SerializerInterface $serializer
	 * @param ConverterInterface $converter
	 */
	public function __construct(QueueInterface $queue, SerializerInterface $serializer, ConverterInterface $converter)
	{
		$this->setQueue($queue);
		$this->setSerializer($serializer);
		$this->setConverter($converter);
	}

	/**
	 * @inheritdoc
	 */
	public function setQueue(QueueInterface $queue)
	{
		$this->queue = $queue;
	}

	/**
	 * @return QueueInterface
	 */
	public function getQueue()
	{
		return $this->queue;
	}

	/**
	 * @inheritdoc
	 */
	public function setSerializer(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}

	/**
	 * @return SerializerInterface
	 */
	public function getSerializer()
	{
		return $this->serializer;
	}

	/**
	 * @param string $format
	 */
	public function setSerializerFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @return string
	 */
	public function getSerializerFormat()
	{
		if ($this->format === null) {
			$this->format = 'json';
		}

		return $this->format;
	}

	/**
	 * @inheritdoc
	 */
	public function setConverter(ConverterInterface $converter)
	{
		$this->converter = $converter;
	}

	/**
	 * @return ConverterInterface
	 */
	public function getConverter()
	{
		return $this->converter;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSubscribedEvents()
	{
		return array(
			Events::postPersist,
			Events::postUpdate,
			Events::postRemove,
		);
	}

	public function postPersist(LifecycleEventArgs $event)
	{
		$this->process('ADD', $event);
	}

	public function postUpdate(LifecycleEventArgs $event)
	{
		$this->process('ADD', $event);
	}

	public function postRemove(LifecycleEventArgs $event)
	{
		$this->process('DELETE', $event);
	}

	protected function process($action, LifecycleEventArgs $event)
	{
		$job = new Job($action);

		switch($job->getAction()) {
			case 'ADD':
				$job->setOption('document.id', $this->getConverter()->getId($event->getDocument()));

				$job->setOption('document.data', $this->getSerializer()->serialize($event->getDocument(), $this->getSerializerFormat()));
				$job->setOption('document.class', get_class($event->getDocument()));
				$job->setOption('document.format', $this->getSerializerFormat());
				break;

			case 'DELETE':
				$job->setOption('id', $this->getConverter()->getId($event->getDocument()));
				break;
		}

		$this->getQueue()->push($job);
	}
}