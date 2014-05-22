<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Locks\Provider\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

use Integrated\Common\Locks\Exception\InvalidArgumentException;
use Integrated\Common\Locks\Exception\UnexpectedTypeException;
use Integrated\Common\Locks\LockInterface;
use Integrated\Common\Locks\ManagerInterface;
use Integrated\Common\Locks\RequestInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Manager implements ManagerInterface
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
	public function acquire(RequestInterface $request, $timeout = 0)
	{
		if ($owner = $request->getOwner()) {
			if ($owner->getIdentifier() === null) {
				throw new InvalidArgumentException('The owner identifier can not be null');
			}
		}

		// @todo POTENTIAL PROBLEM: this uses current server time which
		// could not be in sync and that could lead to problems if locks
		// are created on more then one server.

		$created = time();
		$timeout = $request->getTimeout();
		$expires = $timeout === null ? null : $created + $timeout;

		// if the insert succeeds then the lock is made else setting the
		// lock failed.

		try {
			// have the server created a uuid for this lock

			$data = $this->connection->fetchColumn('SELECT ' . $this->platform->getGuidExpression());
			$data = array_shift($data);

			$data = [
				'id' => $data,
				'resource' => Resource::serialize($request->getResource()),
				'resource_owner' => Resource::serialize($owner),
				'created' => $created,
				'expires' => $expires,
				'timeout' => $request->getTimeout()
			];

			$this->connection->insert($this->options['lock_table_name'], $data);
		} catch (DBALException $e) {
			return null;
		}

		return Lock::factory($data);
	}

	/**
	 * @inheritdoc
	 */
	public function release($lock)
	{
		if ($lock instanceof LockInterface) {
			$lock = $lock->getId();
		} else if (!is_string($lock)) {
			throw new UnexpectedTypeException($lock, 'string or Integrated\Common\Locks\LockInterface');
		}

		// maybe check first if the lock exist ?

		try {
			$this->connection->delete($this->options['lock_table_name'], $lock);
		} catch (DBALException $e) {
			return null; // could not be removed ...
		}
	}

	/**
	 * @inheritdoc
	 */
	public function refresh($lock)
	{
		if ($lock instanceof LockInterface) {
			$lock = $lock->getId();
		} else if (!is_string($lock)) {
			throw new UnexpectedTypeException($lock, 'string or Integrated\Common\Locks\LockInterface');
		}

		try {
			$this->connection->beginTransaction();

			$query = sprintf(
				'SELECT * FROM %s WHERE id = ? %s',
				$this->platform->quoteIdentifier($this->options['lock_table_name']),
				$this->platform->getForUpdateSQL()
			);

			if ($data = $this->connection->fetchAssoc($query, [$lock])) {
				if ($data['timeout'] !== null) {
					$data['expires'] = time() + $data['timeout'];

					$this->connection->update($this->options['lock_table_name'], ['expires' => $data['expires']], ['id' => $data['id']]);
				}
			}

			$this->connection->commit();
		} catch (DBALException $e) {
			$this->connection->rollBack();

			return null; // probably raise a error
		}

		if ($data) {
			return Lock::factory($data);
		}

		return null;
	}

	/**
	 * Remove all the locks set by this provider
	 */
	public function clear()
	{
		try {
			// Truncate can safely be used as ids are uuid what should always be
			// unique so there should be no issues with reusing the same key that
			// auto id could have.

			$this->connection->executeQuery($this->platform->getTruncateTableSQL($this->options['lock_table_name']));
		} catch (DBALException $e) {
			// probably should raise a error
		}
	}

	/**
	 * Remove all the expired lock set by this provider
	 */
	public function clean()
	{
		try {

			$query = sprintf(
				'DELETE FROM %s WHERE expires NOT NULL AND expires < ?',
				$this->platform->quoteIdentifier($this->options['lock_table_name'])
			);

			$this->connection->executeQuery($query, [time()]);
		} catch (DBALException $e) {
			// probably should raise a error
		}
	}
}