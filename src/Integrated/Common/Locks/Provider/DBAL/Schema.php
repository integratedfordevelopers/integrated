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
use Doctrine\DBAL\Schema\Schema as BaseSchema;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class Schema extends BaseSchema
{
	private $options;

	public function __construct(array $options, Connection $connection = null)
	{
		$schemaConfig = $connection ? null : $connection->getSchemaManager()->createSchema();

		parent::__construct(array(), array(), $schemaConfig);

		$this->options = $options;

		$this->addLockTable();
	}

	/**
	 * Merges Locking schema with the given schema.
	 *
	 * @param BaseSchema $schema
	 */
	public function addToSchema(BaseSchema $schema)
	{
		foreach ($this->getTables() as $table) {
			$schema->_addTable($table);
		}

		foreach ($this->getSequences() as $sequence) {
			$schema->_addSequence($sequence);
		}
	}

	protected function addLockTable()
	{
		$table = $this->createTable($this->options['lock_table_name']);

		$table->addColumn('id', 'string', ['length' => 36]);
		$table->addColumn('resource', 'string', ['length' => 200]);
		$table->addColumn('resource_owner', 'string', ['length' => 200, 'notnull' => false]);
		$table->addColumn('created', 'integer', ['unsigned' => true]);
		$table->addColumn('expires', 'integer', ['unsigned' => true, 'notnull' => false]);
		$table->addColumn('timeout', 'integer', ['unsigned' => true, 'notnull' => false]);

		$table->setPrimaryKey(['id']);

		$table->addUniqueIndex(['resource']);

		$table->addIndex(['resource_owner']);
		$table->addIndex(['expires']);
	}
}