<?php

declare(strict_types=1);

namespace Integrated\Bundle\InstallerBundle\Migrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190102214020 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE IF NOT EXISTS queue (
              id INT UNSIGNED AUTO_INCREMENT NOT NULL,
              channel VARCHAR(50) NOT NULL,
              payload LONGTEXT NOT NULL,
              priority SMALLINT NOT NULL,
              attempts SMALLINT UNSIGNED NOT NULL,
              time_created INT UNSIGNED NOT NULL,
              time_updated INT UNSIGNED NOT NULL,
              time_execute INT UNSIGNED NOT NULL,
              INDEX IDX_7FFD7F63A2F98E47ABE4B1B862A6DC27BF396750 (channel, time_execute, priority, id),
              PRIMARY KEY(id)
          ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE queue');
    }
}
