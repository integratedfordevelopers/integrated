<?php

namespace Integrated\Bundle\InstallerBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use MongoDB\Database;

final class Version20201214122713 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function up(Database $db)
    {
        $db->selectCollection('page')->createIndexes([
            ['key' => ['channel.$id' => 1]],
            ['key' => ['contentType.$id' => 1]],
            ['key' => ['class' => 1]],
        ]);
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->selectCollection('page')->dropIndexes([
            ['key' => ['channel.$id' => 1]],
            ['key' => ['contentType.$id' => 1]],
            ['key' => ['class' => 1]],
        ]);
    }
}
