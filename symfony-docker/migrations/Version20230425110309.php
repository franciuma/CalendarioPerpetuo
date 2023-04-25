<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425110309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX provincia_idx ON calendario');
        $this->addSql('DROP INDEX nombre_idx ON calendario');
        $this->addSql('ALTER TABLE calendario DROP nombre, DROP provincia');
        $this->addSql('ALTER TABLE centro ADD calendario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE centro ADD CONSTRAINT FK_2675036BA7F6EA19 FOREIGN KEY (calendario_id) REFERENCES calendario (id)');
        $this->addSql('CREATE INDEX IDX_2675036BA7F6EA19 ON centro (calendario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendario ADD nombre VARCHAR(255) NOT NULL, ADD provincia VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX provincia_idx ON calendario (provincia)');
        $this->addSql('CREATE INDEX nombre_idx ON calendario (nombre)');
        $this->addSql('ALTER TABLE centro DROP FOREIGN KEY FK_2675036BA7F6EA19');
        $this->addSql('DROP INDEX IDX_2675036BA7F6EA19 ON centro');
        $this->addSql('ALTER TABLE centro DROP calendario_id');
    }
}
