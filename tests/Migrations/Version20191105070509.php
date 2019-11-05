<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191105070509 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_20FDDF4E7E3C61F9');
        $this->addSql('DROP INDEX IDX_20FDDF4E3DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__verification_request AS SELECT id, image_id, owner_id, status, message, rejection_reason, created_at, updated_at FROM verification_request');
        $this->addSql('DROP TABLE verification_request');
        $this->addSql('CREATE TABLE verification_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, owner_id INTEGER NOT NULL, status SMALLINT NOT NULL, message VARCHAR(255) DEFAULT NULL COLLATE BINARY, rejection_reason VARCHAR(255) DEFAULT NULL COLLATE BINARY, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT FK_20FDDF4E3DA5256D FOREIGN KEY (image_id) REFERENCES media_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_20FDDF4E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO verification_request (id, image_id, owner_id, status, message, rejection_reason, created_at, updated_at) SELECT id, image_id, owner_id, status, message, rejection_reason, created_at, updated_at FROM __temp__verification_request');
        $this->addSql('DROP TABLE __temp__verification_request');
        $this->addSql('CREATE INDEX IDX_20FDDF4E3DA5256D ON verification_request (image_id)');
        $this->addSql('CREATE INDEX IDX_20FDDF4E7E3C61F9 ON verification_request (owner_id)');
        $this->addSql('DROP INDEX IDX_5A8A6C8D7E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, owner_id, title, content, created_at, updated_at FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, content CLOB NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT FK_5A8A6C8D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post (id, owner_id, title, content, created_at, updated_at) SELECT id, owner_id, title, content, created_at, updated_at FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D7E3C61F9 ON post (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_5A8A6C8D7E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, owner_id, title, content, created_at, updated_at FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO post (id, owner_id, title, content, created_at, updated_at) SELECT id, owner_id, title, content, created_at, updated_at FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D7E3C61F9 ON post (owner_id)');
        $this->addSql('DROP INDEX IDX_20FDDF4E3DA5256D');
        $this->addSql('DROP INDEX IDX_20FDDF4E7E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__verification_request AS SELECT id, image_id, owner_id, status, message, rejection_reason, created_at, updated_at FROM verification_request');
        $this->addSql('DROP TABLE verification_request');
        $this->addSql('CREATE TABLE verification_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, owner_id INTEGER NOT NULL, status SMALLINT NOT NULL, message VARCHAR(255) DEFAULT NULL, rejection_reason VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO verification_request (id, image_id, owner_id, status, message, rejection_reason, created_at, updated_at) SELECT id, image_id, owner_id, status, message, rejection_reason, created_at, updated_at FROM __temp__verification_request');
        $this->addSql('DROP TABLE __temp__verification_request');
        $this->addSql('CREATE INDEX IDX_20FDDF4E3DA5256D ON verification_request (image_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_20FDDF4E7E3C61F9 ON verification_request (owner_id)');
    }
}
