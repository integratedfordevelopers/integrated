<?php declare(strict_types=1);

namespace Integrated\Bundle\InstallerBundle\Migrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190102214021 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS `locks` (`id` varchar(36) COLLATE utf8_unicode_ci NOT NULL, `resource` varchar(200) COLLATE utf8_unicode_ci NOT NULL, `resource_owner` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL, `created` int(10) UNSIGNED NOT NULL, `expires` int(10) UNSIGNED DEFAULT NULL, `timeout` int(10) UNSIGNED DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $this->addSql('ALTER TABLE `locks` ADD PRIMARY KEY (`id`),  ADD UNIQUE KEY `UNIQ_FC316D97BC91F416` (`resource`),  ADD KEY `IDX_FC316D97E4DB9C4E` (`resource_owner`),  ADD KEY `IDX_FC316D979A9C688C` (`expires`);');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE locks');
    }
}
