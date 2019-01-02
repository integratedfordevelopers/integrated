<?php declare(strict_types=1);

namespace Integrated\Bundle\InstallerBundle\Migrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190102214019 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(
            $schema->hasTable('security_users'),
            'Skipping because Integrated tables exist (this is normal when upgrading from Integrated < 0.11)'
        );

        $this->addSql('CREATE TABLE IF NOT EXISTS workflow_states (id INT AUTO_INCREMENT NOT NULL, state_id VARCHAR(36) DEFAULT NULL, content_id VARCHAR(50) NOT NULL, content_class VARCHAR(255) NOT NULL, assigned_id VARCHAR(50) DEFAULT NULL, assigned_class VARCHAR(255) DEFAULT NULL, assigned_type VARCHAR(5) DEFAULT NULL, deadline DATETIME DEFAULT NULL, INDEX IDX_10C7CD8E5D83CC1 (state_id), UNIQUE INDEX UNIQ_10C7CD8E84A0A3ED6884D4D (content_id, content_class), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS workflow_definition (id VARCHAR(36) NOT NULL, default_state_id VARCHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F51FF1ED5E237E06 (name), INDEX IDX_F51FF1ED39C0C8F (default_state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS workflow_definition_state_permissions (group_id VARCHAR(255) NOT NULL, state_id VARCHAR(36) NOT NULL, mask INT NOT NULL, INDEX IDX_DA68BC085D83CC1 (state_id), PRIMARY KEY(group_id, state_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS workflow_definition_states (id VARCHAR(36) NOT NULL, workflow_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, sort INT NOT NULL, publishable TINYINT(1) NOT NULL, comment INT NOT NULL, assignee INT NOT NULL, deadline INT NOT NULL, INDEX IDX_27407B632C7C2CBA (workflow_id), UNIQUE INDEX UNIQ_27407B632C7C2CBA5E237E06 (workflow_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS workflow_definition_state_transitions (state_id VARCHAR(36) NOT NULL, state_transition_id VARCHAR(36) NOT NULL, INDEX IDX_67C212215D83CC1 (state_id), INDEX IDX_67C21221B8E7AC88 (state_transition_id), PRIMARY KEY(state_id, state_transition_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS workflow_history (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, state_id VARCHAR(36) DEFAULT NULL, timestamp DATETIME NOT NULL, user_id VARCHAR(50) DEFAULT NULL, user_class VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, deadline DATETIME DEFAULT NULL, INDEX IDX_25F6E6FB7E3C61F9 (owner_id), INDEX IDX_25F6E6FB5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS security_scopes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_323B15255E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS security_users (id INT AUTO_INCREMENT NOT NULL, scope INT DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, password_salt VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, locked TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, relation VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F83F4643E7927C74 (email), INDEX IDX_F83F4643AF55D3 (scope), UNIQUE INDEX UNIQ_F83F4643F85E0677AF55D3 (username, scope), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS security_user_groups (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B590752CA76ED395 (user_id), INDEX IDX_B590752CFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS security_user_roles (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_942E07EAA76ED395 (user_id), INDEX IDX_942E07EAD60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS security_roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, hidden TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_5A82CD6D5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS security_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C682CF655E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS security_group_roles (group_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_C6BD52C1FE54D947 (group_id), INDEX IDX_C6BD52C1D60322AC (role_id), PRIMARY KEY(group_id, role_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS channel_connector_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, adapter VARCHAR(255) NOT NULL, options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', channels JSON NOT NULL COMMENT \'(DC2Type:json_array)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE workflow_states ADD CONSTRAINT FK_10C7CD8E5D83CC1 FOREIGN KEY (state_id) REFERENCES workflow_definition_states (id)');
        $this->addSql('ALTER TABLE workflow_definition ADD CONSTRAINT FK_F51FF1ED39C0C8F FOREIGN KEY (default_state_id) REFERENCES workflow_definition_states (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE workflow_definition_state_permissions ADD CONSTRAINT FK_DA68BC085D83CC1 FOREIGN KEY (state_id) REFERENCES workflow_definition_states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workflow_definition_states ADD CONSTRAINT FK_27407B632C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow_definition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workflow_definition_state_transitions ADD CONSTRAINT FK_67C212215D83CC1 FOREIGN KEY (state_id) REFERENCES workflow_definition_states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workflow_definition_state_transitions ADD CONSTRAINT FK_67C21221B8E7AC88 FOREIGN KEY (state_transition_id) REFERENCES workflow_definition_states (id)');
        $this->addSql('ALTER TABLE workflow_history ADD CONSTRAINT FK_25F6E6FB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES workflow_states (id)');
        $this->addSql('ALTER TABLE workflow_history ADD CONSTRAINT FK_25F6E6FB5D83CC1 FOREIGN KEY (state_id) REFERENCES workflow_definition_states (id)');
        $this->addSql('ALTER TABLE security_users ADD CONSTRAINT FK_F83F4643AF55D3 FOREIGN KEY (scope) REFERENCES security_scopes (id)');
        $this->addSql('ALTER TABLE security_user_groups ADD CONSTRAINT FK_B590752CA76ED395 FOREIGN KEY (user_id) REFERENCES security_users (id)');
        $this->addSql('ALTER TABLE security_user_groups ADD CONSTRAINT FK_B590752CFE54D947 FOREIGN KEY (group_id) REFERENCES security_groups (id)');
        $this->addSql('ALTER TABLE security_user_roles ADD CONSTRAINT FK_942E07EAA76ED395 FOREIGN KEY (user_id) REFERENCES security_users (id)');
        $this->addSql('ALTER TABLE security_user_roles ADD CONSTRAINT FK_942E07EAD60322AC FOREIGN KEY (role_id) REFERENCES security_roles (id)');
        $this->addSql('ALTER TABLE security_group_roles ADD CONSTRAINT FK_C6BD52C1FE54D947 FOREIGN KEY (group_id) REFERENCES security_groups (id)');
        $this->addSql('ALTER TABLE security_group_roles ADD CONSTRAINT FK_C6BD52C1D60322AC FOREIGN KEY (role_id) REFERENCES security_roles (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE workflow_history DROP FOREIGN KEY FK_25F6E6FB7E3C61F9');
        $this->addSql('ALTER TABLE workflow_definition_states DROP FOREIGN KEY FK_27407B632C7C2CBA');
        $this->addSql('ALTER TABLE workflow_states DROP FOREIGN KEY FK_10C7CD8E5D83CC1');
        $this->addSql('ALTER TABLE workflow_definition DROP FOREIGN KEY FK_F51FF1ED39C0C8F');
        $this->addSql('ALTER TABLE workflow_definition_state_permissions DROP FOREIGN KEY FK_DA68BC085D83CC1');
        $this->addSql('ALTER TABLE workflow_definition_state_transitions DROP FOREIGN KEY FK_67C212215D83CC1');
        $this->addSql('ALTER TABLE workflow_definition_state_transitions DROP FOREIGN KEY FK_67C21221B8E7AC88');
        $this->addSql('ALTER TABLE workflow_history DROP FOREIGN KEY FK_25F6E6FB5D83CC1');
        $this->addSql('ALTER TABLE security_users DROP FOREIGN KEY FK_F83F4643AF55D3');
        $this->addSql('ALTER TABLE security_user_groups DROP FOREIGN KEY FK_B590752CA76ED395');
        $this->addSql('ALTER TABLE security_user_roles DROP FOREIGN KEY FK_942E07EAA76ED395');
        $this->addSql('ALTER TABLE security_user_roles DROP FOREIGN KEY FK_942E07EAD60322AC');
        $this->addSql('ALTER TABLE security_group_roles DROP FOREIGN KEY FK_C6BD52C1D60322AC');
        $this->addSql('ALTER TABLE security_user_groups DROP FOREIGN KEY FK_B590752CFE54D947');
        $this->addSql('ALTER TABLE security_group_roles DROP FOREIGN KEY FK_C6BD52C1FE54D947');
        $this->addSql('DROP TABLE workflow_states');
        $this->addSql('DROP TABLE workflow_definition');
        $this->addSql('DROP TABLE workflow_definition_state_permissions');
        $this->addSql('DROP TABLE workflow_definition_states');
        $this->addSql('DROP TABLE workflow_definition_state_transitions');
        $this->addSql('DROP TABLE workflow_history');
        $this->addSql('DROP TABLE security_scopes');
        $this->addSql('DROP TABLE security_users');
        $this->addSql('DROP TABLE security_user_groups');
        $this->addSql('DROP TABLE security_user_roles');
        $this->addSql('DROP TABLE security_roles');
        $this->addSql('DROP TABLE security_groups');
        $this->addSql('DROP TABLE security_group_roles');
        $this->addSql('DROP TABLE channel_connector_config');
    }
}
