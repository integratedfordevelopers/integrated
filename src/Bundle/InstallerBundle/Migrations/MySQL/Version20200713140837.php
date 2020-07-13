<?php

declare(strict_types=1);

namespace Integrated\Bundle\InstallerBundle\Migrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200713140837 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE scraper (
            id INT AUTO_INCREMENT NOT NULL,
            created_at DATETIME NOT NULL,
            name VARCHAR(255) DEFAULT NULL,
            channel_id VARCHAR(80) NOT NULL,
            template_name VARCHAR(800) NOT NULL,
            url VARCHAR(800) DEFAULT NULL,
            template LONGTEXT DEFAULT NULL,
            last_modified INT NOT NULL,
            last_error VARCHAR(800) DEFAULT NULL,
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scraper_block_link (
            scraper_id INT NOT NULL,
            block_id INT NOT NULL,
            INDEX IDX_E45CDE15A68BBF9 (scraper_id),
            INDEX IDX_E45CDE1E9ED820C (block_id),
            PRIMARY KEY(scraper_id, block_id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scraper_block (
            id INT AUTO_INCREMENT NOT NULL,
            created_at DATETIME NOT NULL,
            name VARCHAR(255) DEFAULT NULL,
            mode VARCHAR(255) DEFAULT NULL,
            selector VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scraper_block_link ADD CONSTRAINT FK_E45CDE15A68BBF9 FOREIGN KEY (scraper_id) REFERENCES scraper (id)');
        $this->addSql('ALTER TABLE scraper_block_link ADD CONSTRAINT FK_E45CDE1E9ED820C FOREIGN KEY (block_id) REFERENCES scraper_block (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scraper_block_link DROP FOREIGN KEY FK_E45CDE15A68BBF9');
        $this->addSql('ALTER TABLE scraper_block_link DROP FOREIGN KEY FK_E45CDE1E9ED820C');
        $this->addSql('DROP TABLE scraper');
        $this->addSql('DROP TABLE scraper_block_link');
        $this->addSql('DROP TABLE scraper_block');
    }
}
