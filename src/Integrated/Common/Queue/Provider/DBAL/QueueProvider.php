<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Queue\Provider\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;

use Integrated\Common\Queue\Provider\QueueProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueProvider implements QueueProviderInterface
{
	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var Connection
	 */
	protected $connection;

	/**
	 * @var AbstractPlatform
	 */
	protected $platform;

	/**
	 * @param Connection $connection
	 * @param array $options
	 */
	public function __construct(Connection $connection, array $options)
	{
		$this->connection = $connection;
		$this->platform = $connection->getDatabasePlatform();
		$this->options = $options;
	}

	/**
	 * {@inheritdoc}
	 */
	public function push($channel, $payload, $delay = 0, $priority = 0)
	{
		$channel = (string) $channel;
		$payload = serialize($payload);
		$timestamp = time();
		$delay = (int) $delay;
		$priority = min(max((int) $priority, -10), 10);

		$this->connection->insert($this->options['queue_table_name'], [
			'channel'      => $channel,
			'payload'      => $payload,
			'priority'     => $priority,
			'attempts'     => 0,
			'time_created' => $timestamp,
			'time_updated' => $timestamp,
			'time_execute' => $timestamp + $delay,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function pull($channel, $limit = 1)
	{
		$channel = (string) $channel;

		$limit = (int) $limit;
		$limit = $limit > 1 ? $limit : 1;

		$query = '
			SELECT id, payload,	attempts, priority
			FROM %s
			WHERE channel = ? AND time_execute <= ?
			ORDER BY priority DESC, time_execute, id
		';

		$query = sprintf(
			$this->platform->modifyLimitQuery($query, $limit),
			$this->platform->quoteIdentifier($this->options['queue_table_name'])
		);

		$results = array();

		foreach ($this->connection->fetchAll($query, [$channel, time()]) as $row) {
			$delete = function() use ($row) {
				$this->delete($row['id']);
			};

			$release = function($delay) use ($row) {
				$this->release($row['id'], $delay);
			};

			$results[] = new QueueMessage($row, $delete, $release);
		}

		return $results;
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear($channel)
	{
		$channel = (string) $channel;

		$this->connection->delete($this->options['queue_table_name'], ['channel' => $channel]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function count($channel = null)
	{
		$query = 'SELECT COUNT(id) AS count FROM %s';
		$query = sprintf(
			$query,
			$this->platform->quoteIdentifier($this->options['queue_table_name'])
		);

        if ($channel) {
            $channel = (string) $channel;
            $query .= ' WHERE channel = ?';
        }

		return $this->connection->fetchColumn($query, [$channel]);
	}

	/**
	 * Delete the message from the queue
	 *
	 * @param string $id
	 */
	protected function delete($id)
	{
		$this->connection->delete($this->options['queue_table_name'], ['id' => (string) $id]);
	}

	protected function release($id, $delay = 0)
	{
		// @todo still needs to be implemented but at the moment records are not locked in the first place.
	}
}
