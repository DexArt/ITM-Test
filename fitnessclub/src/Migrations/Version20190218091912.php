<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190218091912 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE user ADD COLUMN is_active BOOLEAN DEFAULT \'0\' NOT NULL');
        $this->addSql('DROP INDEX IDX_359F6E8F9B458710');
        $this->addSql('DROP INDEX IDX_359F6E8FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_training AS SELECT id, user_id, group_training_id FROM user_training');
        $this->addSql('DROP TABLE user_training');
        $this->addSql('CREATE TABLE user_training (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, group_training_id INTEGER NOT NULL, CONSTRAINT FK_359F6E8FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_359F6E8F9B458710 FOREIGN KEY (group_training_id) REFERENCES group_training (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_training (id, user_id, group_training_id) SELECT id, user_id, group_training_id FROM __temp__user_training');
        $this->addSql('DROP TABLE __temp__user_training');
        $this->addSql('CREATE INDEX IDX_359F6E8F9B458710 ON user_training (group_training_id)');
        $this->addSql('CREATE INDEX IDX_359F6E8FA76ED395 ON user_training (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised, is_deleted FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, firstname VARCHAR(60) NOT NULL, lastname VARCHAR(60) NOT NULL, email VARCHAR(60) NOT NULL, password VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, birthday DATETIME NOT NULL, phone VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , auth_token VARCHAR(255) DEFAULT NULL, is_authorised BOOLEAN DEFAULT NULL, is_deleted BOOLEAN DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised, is_deleted) SELECT id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised, is_deleted FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('DROP INDEX IDX_359F6E8FA76ED395');
        $this->addSql('DROP INDEX IDX_359F6E8F9B458710');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_training AS SELECT id, user_id, group_training_id FROM user_training');
        $this->addSql('DROP TABLE user_training');
        $this->addSql('CREATE TABLE user_training (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, group_training_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO user_training (id, user_id, group_training_id) SELECT id, user_id, group_training_id FROM __temp__user_training');
        $this->addSql('DROP TABLE __temp__user_training');
        $this->addSql('CREATE INDEX IDX_359F6E8FA76ED395 ON user_training (user_id)');
        $this->addSql('CREATE INDEX IDX_359F6E8F9B458710 ON user_training (group_training_id)');
    }
}
