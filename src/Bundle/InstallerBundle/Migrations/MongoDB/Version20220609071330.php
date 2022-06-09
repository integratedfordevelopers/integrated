<?php

namespace Integrated\Bundle\InstallerBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use MongoDB\Database;

final class Version20220609071330 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Convert max endDate to UTC timezone';
    }

    /**
     * {@inheritDoc}
     */
    public function up(Database $db)
    {
        $db->selectCollection('content')->updateMany(
            ['publishTime.endDate' => new \MongoDB\BSON\UTCDateTime(253402210800000)],
            ['$set' => ['publishTime.endDate' => new \MongoDB\BSON\UTCDateTime(253402214400000)]]
        );
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->selectCollection('content')->updateMany(
            ['publishTime.endDate' => new \MongoDB\BSON\UTCDateTime(253402214400000)],
            ['$set' => ['publishTime.endDate' => new \MongoDB\BSON\UTCDateTime(253402210800000)]]
        );
    }
}
