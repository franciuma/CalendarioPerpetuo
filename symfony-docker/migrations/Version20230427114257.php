<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230427114257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE titulacion (id INT AUTO_INCREMENT NOT NULL, nombre_titulacion VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asignatura ADD titulacion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asignatura ADD CONSTRAINT FK_9243D6CEF471CF55 FOREIGN KEY (titulacion_id) REFERENCES titulacion (id)');
        $this->addSql('CREATE INDEX IDX_9243D6CEF471CF55 ON asignatura (titulacion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asignatura DROP FOREIGN KEY FK_9243D6CEF471CF55');
        $this->addSql('DROP TABLE titulacion');
        $this->addSql('DROP INDEX IDX_9243D6CEF471CF55 ON asignatura');
        $this->addSql('ALTER TABLE asignatura DROP titulacion_id');
    }
}
