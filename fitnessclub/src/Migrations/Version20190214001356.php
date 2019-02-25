<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190214001356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_8D93D64935C246D5');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, firstname VARCHAR(60) NOT NULL COLLATE BINARY, lastname VARCHAR(60) NOT NULL COLLATE BINARY, email VARCHAR(60) NOT NULL COLLATE BINARY, password VARCHAR(255) NOT NULL COLLATE BINARY, gender VARCHAR(255) NOT NULL COLLATE BINARY, birthday DATETIME NOT NULL, phone INTEGER NOT NULL, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , auth_token VARCHAR(255) DEFAULT NULL COLLATE BINARY, is_authorised BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO user (id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised) SELECT id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64935C246D5 ON user (password)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D64935C246D5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, firstname VARCHAR(60) NOT NULL, lastname VARCHAR(60) NOT NULL, email VARCHAR(60) NOT NULL, password VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, birthday DATETIME NOT NULL, phone INTEGER NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , auth_token VARCHAR(255) DEFAULT NULL, is_authorised BOOLEAN DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised) SELECT id, firstname, lastname, email, password, gender, birthday, phone, roles, auth_token, is_authorised FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64935C246D5 ON user (password)');
    }
}
