<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308174457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clase (id INT AUTO_INCREMENT NOT NULL, calendario_id INT DEFAULT NULL, inicio TIME NOT NULL, final TIME NOT NULL, lugar VARCHAR(255) NOT NULL, correo VARCHAR(255) NOT NULL, asignatura VARCHAR(255) NOT NULL, INDEX IDX_199FACCEA7F6EA19 (calendario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clase ADD CONSTRAINT FK_199FACCEA7F6EA19 FOREIGN KEY (calendario_id) REFERENCES calendario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase DROP FOREIGN KEY FK_199FACCEA7F6EA19');
        $this->addSql('DROP TABLE clase');
    }
}
