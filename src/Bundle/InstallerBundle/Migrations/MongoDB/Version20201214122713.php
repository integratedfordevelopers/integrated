<?php

namespace Integrated\Bundle\InstallerBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

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
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->selectCollection('page')->ensureIndex(['channel.$id' => 1, 'contentType.$id' => 1, 'class' => 1]);
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->selectCollection('page')->deleteIndex(['channel.$id' => 1, 'contentType.$id' => 1, 'class' => 1]);
    }
}
