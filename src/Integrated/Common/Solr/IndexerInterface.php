<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr;

use Solarium\Core\Client\Client;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface IndexerInterface
{
	public function setQueue(QueueInterface $queue);

	public function setSolr(Client $client);

	public function execute();
}