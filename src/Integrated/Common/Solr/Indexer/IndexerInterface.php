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

use Solarium\Core\Client\Client;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface IndexerInterface extends SerializerAwareInterface
{
	/**
	 * @param QueueInterface $queue
	 */
	public function setQueue(QueueInterface $queue);

	/**
	 * @param Client $client
	 */
	public function setClient(Client $client);

	/**
	 * @param SerializerInterface $serializer
	 */
	public function setSerializer(SerializerInterface $serializer);

	/**
	 * @param Client $client
	 * @return mixed
	 */
	public function execute(Client $client = null);
}