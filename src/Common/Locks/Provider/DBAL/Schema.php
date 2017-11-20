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

    public function __construct(array $options, Connection $connection = null)
    {
        $schemaConfig = $connection ? null : $connection->getSchemaManager()->createSchema();

        parent::__construct([], [], $schemaConfig);

        $this->options = $options;

        $this->addLockTable();
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
