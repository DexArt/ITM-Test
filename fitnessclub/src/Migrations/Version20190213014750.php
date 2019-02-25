<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190213014750 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE user ADD COLUMN roles CLOB NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D64935C246D5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, firstname, lastname, email, password, gender, birthday, phone FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, firstname VARCHAR(60) NOT NULL, lastname VARCHAR(60) NOT NULL, email VARCHAR(60) NOT NULL, password VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, birthday DATETIME NOT NULL, phone INTEGER NOT NULL)');
        $this->addSql('INSERT INTO user (id, firstname, lastname, email, password, gender, birthday, phone) SELECT id, firstname, lastname, email, password, gender, birthday, phone FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64935C246D5 ON user (password)');
    }
}
