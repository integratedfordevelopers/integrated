<?php

namespace Integrated\Bundle\InstallerBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

final class Version20200102214019 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Indexes";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->selectCollection('block')->ensureIndex(['class' => 1]);

        $db->selectCollection('content')->ensureIndex(['class' => 1]);
        $db->selectCollection('content')->ensureIndex(['relations.references.$id' => 1, 'class' => 1]);
        $db->selectCollection('content')->ensureIndex(['slug' => 1]);
        $db->selectCollection('content')->ensureIndex(['contentType' => 1]);
        $db->selectCollection('content')->ensureIndex(['relations.relationId' => 1]);
        $db->selectCollection('content')->ensureIndex(['relations.relationType' => 1]);

        $db->selectCollection('content_history')->ensureIndex(['contentId' => 1, 'date' => 1]);
        $db->selectCollection('content_history')->ensureIndex(['user.id' => 1]);
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->selectCollection('block')->deleteIndex(['class' => 1]);

        $db->selectCollection('content')->deleteIndex(['class' => 1]);
        $db->selectCollection('content')->deleteIndex(['relations.references.$id' => 1, 'class' => 1]);
        $db->selectCollection('content')->deleteIndex(['slug' => 1]);
        $db->selectCollection('content')->deleteIndex(['contentType' => 1]);
        $db->selectCollection('content')->deleteIndex(['relations.relationId' => 1]);
        $db->selectCollection('content')->deleteIndex(['relations.relationType' => 1]);

        $db->selectCollection('content_history')->deleteIndex(['contentId' => 1, 'date' => 1]);
        $db->selectCollection('content_history')->deleteIndex(['user.id' => 1]);
    }
}
