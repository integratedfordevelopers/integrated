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

use DateTime;
use DateInterval;

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
	 * @inheritdoc
	 */
	public function push($channel, $payload, $delay = 0)
	{
		$channel = (string) $channel;
		$payload = serialize($payload);
		$timestamp = time();
		$delay = (int) $delay;

		$query = '
			INSERT INTO %s (id, channel, payload, attempts, time_created, time_updated, time_execute)
			VALUES (%s, ?, ?, 0, ?, ?, ?)
		';

		$query = sprintf(
			$query,
			$this->platform->quoteIdentifier($this->options['queue_table_name']),
			$this->platform->getGuidExpression()
		);

		$statement = $this->connection->prepare($query);
		$statement->execute([$channel, $payload, $timestamp, $timestamp, ($timestamp + $delay)]);
	}

	/**
	 * @inheritdoc
	 */
	public function pull($channel, $limit = 1)
	{
		$channel = (string) $channel;

		$limit = (int) $limit;
		$limit = $limit > 1 ? $limit : 1;

//		$query = '
//			UPDATE %s
//			SET attempts = attempts + 1
//			WHERE channel = ? AND time_execute <= ?
//			ORDER BY time_execute, id
//		';
//
//		$query = sprintf(
//			$this->platform->modifyLimitQuery($query, $limit),
//			$this->platform->quoteIdentifier($this->options['queue_table_name'])
//		);

		$query = '
			SELECT id, payload,	attempts
			FROM %s
			WHERE channel = ? AND time_execute <= ?
			ORDER BY time_execute, id
		';

		$query = sprintf(
			$this->platform->modifyLimitQuery($query, $limit),
			$this->platform->quoteIdentifier($this->options['queue_table_name'])
		);

		$statement = $this->connection->prepare($query);
		$statement->execute([$channel, time()]);

		$results = array();

		foreach ($statement->fetchAll() as $row) {
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
	 * @inheritdoc
	 */
	public function clear($channel)
	{
		$channel = (string) $channel;

		$query = 'DELETE FROM %s WHERE channel = ?';
		$query = sprintf(
			$query,
			$this->platform->quoteIdentifier($this->options['queue_table_name'])
		);

		$statement = $this->connection->prepare($query);
		$statement->execute([$channel]);
	}

	/**
	 * @inheritdoc
	 */
	public function count($channel)
	{
		$channel = (string) $channel;

		$query = 'SELECT %s AS count FROM %s WHERE channel = ?';
		$query = sprintf(
			$query,
			$this->platform->quoteIdentifier($this->options['queue_table_name'])
		);

		$statement = $this->connection->prepare($query);
		$statement->execute([$channel]);

		$result = $statement->fetchColumn();

		$statement->closeCursor();

		return $result;
	}

	protected function delete($id)
	{
		$id = (string) $id;

		$query = 'DELETE FROM %s WHERE id = ?';
		$query = sprintf(
			$query,
			$this->platform->quoteIdentifier($this->options['queue_table_name'])
		);

		$statement = $this->connection->prepare($query);
		$statement->execute([$id]);
	}

	protected function release($id, $delay = 0)
	{
//		$id = (string) $id;
//
//		$query = 'DELETE FROM %s WHERE id = ?';
//		$query = sprintf(
//			$query,
//			$this->platform->quoteIdentifier($this->options['queue_table_name'])
//		);
//
//		$statement = $this->connection->prepare($query);
//		$statement->execute([$id]);
	}
}