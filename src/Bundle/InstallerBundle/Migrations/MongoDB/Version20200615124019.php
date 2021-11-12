<?php

namespace Integrated\Bundle\InstallerBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use MongoDB\Database;

final class Version20200615124019 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Indexes';
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->selectCollection('block')->createIndex(['class' => 1]);

        $db->selectCollection('content')->createIndex(['class' => 1]);
        $db->selectCollection('content')->createIndex(['relations.references.$id' => 1, 'class' => 1]);
        $db->selectCollection('content')->createIndex(['slug' => 1], ['unique' => true, 'sparse' => true]);
        $db->selectCollection('content')->createIndex(['contentType' => 1]);
        $db->selectCollection('content')->createIndex(['relations.relationId' => 1]);
        $db->selectCollection('content')->createIndex(['relations.relationType' => 1]);

        $db->selectCollection('content_history')->createIndex(['contentId' => 1, 'date' => 1]);
        $db->selectCollection('content_history')->createIndex(['user.id' => 1]);
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->selectCollection('block')->dropIndexes(['class' => 1]);

        $db->selectCollection('content')->dropIndexes(['class' => 1]);
        $db->selectCollection('content')->dropIndexes(['relations.references.$id' => 1, 'class' => 1]);
        $db->selectCollection('content')->dropIndexes(['slug' => 1]);
        $db->selectCollection('content')->dropIndexes(['contentType' => 1]);
        $db->selectCollection('content')->dropIndexes(['relations.relationId' => 1]);
        $db->selectCollection('content')->dropIndexes(['relations.relationType' => 1]);

        $db->selectCollection('content_history')->dropIndexes(['contentId' => 1, 'date' => 1]);
        $db->selectCollection('content_history')->dropIndexes(['user.id' => 1]);
    }
}
