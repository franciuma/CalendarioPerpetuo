<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422175031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE leccion (id INT AUTO_INCREMENT NOT NULL, asignatura_id INT NOT NULL, titulo VARCHAR(255) NOT NULL, INDEX IDX_712BF9EEC5C70C5B (asignatura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE leccion ADD CONSTRAINT FK_712BF9EEC5C70C5B FOREIGN KEY (asignatura_id) REFERENCES asignatura (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE leccion DROP FOREIGN KEY FK_712BF9EEC5C70C5B');
        $this->addSql('DROP TABLE leccion');
    }
}
