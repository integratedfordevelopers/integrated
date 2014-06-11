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

use Doctrine\DBAL\Schema\Schema as BaseSchema;
use Doctrine\DBAL\Connection;

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

		$this->addQueueTable();
	}


	/**
	 * Merges ACL schema with the given schema.
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

	protected function addQueueTable()
	{
		$table = $this->createTable($this->options['queue_table_name']);

		$table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => 'auto']);
		$table->addColumn('channel', 'string', ['length' => 50]);
		$table->addColumn('payload', 'text');
		$table->addColumn('priority', 'smallint');
		$table->addColumn('attempts', 'smallint', ['unsigned' => true]);
		$table->addColumn('time_created', 'integer', ['unsigned' => true]);
		$table->addColumn('time_updated', 'integer', ['unsigned' => true]);
		$table->addColumn('time_execute', 'integer', ['unsigned' => true]);

		$table->setPrimaryKey(array('id'));

		$table->addIndex(array('channel'));
		$table->addIndex(array('time_execute'));
	}
}