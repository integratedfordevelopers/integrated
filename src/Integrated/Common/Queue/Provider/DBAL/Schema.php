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

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema as BaseSchema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Connection;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class Schema extends BaseSchema
{
	/**
	 * @var array
	 */
	private $options;

	/**
	 * @param array $options
	 * @param Connection $connection
	 */
	public function __construct(array $options, Connection $connection = null)
	{
		$schemaConfig = $connection ? null : $connection->getSchemaManager()->createSchema();

		parent::__construct(array(), array(), $schemaConfig);

		$this->options = $options;

		$this->addQueueTable();
	}

	/**
	 * Return a schema diff.
	 *
	 * @param BaseSchema $schema
	 * @return SchemaDiff
	 */
	public function compare(BaseSchema $schema)
	{
		$self = clone $this;
		$self->merge($schema);

		return Comparator::compareSchemas($schema, $self);
	}

	/**
	 * Merge the given schema into this one.
	 *
	 * This schema is leading in case of a conflict.
	 *
	 * @param BaseSchema $schema
	 */
	public function merge(BaseSchema $schema)
	{
		foreach ($schema->getTables() as $table) {
			if (!$this->hasTable($table->getName())) {
				$this->_addTable(clone $table);
			}
		}

		foreach ($schema->getSequences() as $sequence) {
			if (!$this->hasSequence($sequence->getName())) {
				$this->_addSequence(clone $sequence);
			}
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