<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190218173849 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_359F6E8FD0520624');
        $this->addSql('DROP INDEX IDX_359F6E8FA76ED395');
        $this->addSql('DROP INDEX IDX_359F6E8F9B458710');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_training AS SELECT id, user_id, group_training_id FROM user_training');
        $this->addSql('DROP TABLE user_training');
        $this->addSql('CREATE TABLE user_training (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_training_id INTEGER NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_359F6E8F9B458710 FOREIGN KEY (group_training_id) REFERENCES group_training (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_training (id, user_id, group_training_id) SELECT id, user_id, group_training_id FROM __temp__user_training');
        $this->addSql('DROP TABLE __temp__user_training');
        $this->addSql('CREATE INDEX IDX_359F6E8FA76ED395 ON user_training (user_id)');
        $this->addSql('CREATE INDEX IDX_359F6E8F9B458710 ON user_training (group_training_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_359F6E8FA76ED395');
        $this->addSql('DROP INDEX IDX_359F6E8F9B458710');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_training AS SELECT id, group_training_id, user_id FROM user_training');
        $this->addSql('DROP TABLE user_training');
        $this->addSql('CREATE TABLE user_training (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_training_id INTEGER NOT NULL, user_id INTEGER NOT NULL, notification_type_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO user_training (id, group_training_id, user_id) SELECT id, group_training_id, user_id FROM __temp__user_training');
        $this->addSql('DROP TABLE __temp__user_training');
        $this->addSql('CREATE INDEX IDX_359F6E8FA76ED395 ON user_training (user_id)');
        $this->addSql('CREATE INDEX IDX_359F6E8F9B458710 ON user_training (group_training_id)');
        $this->addSql('CREATE INDEX IDX_359F6E8FD0520624 ON user_training (notification_type_id)');
    }
}
