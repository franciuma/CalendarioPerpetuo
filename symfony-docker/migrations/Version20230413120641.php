<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413120641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asignatura (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grupo (id INT AUTO_INCREMENT NOT NULL, asignatura_id INT NOT NULL, letra VARCHAR(255) NOT NULL, dias_teoria VARCHAR(255) DEFAULT NULL, dias_practica VARCHAR(255) DEFAULT NULL, INDEX IDX_8C0E9BD3C5C70C5B (asignatura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profesor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, primer_apellido VARCHAR(255) DEFAULT NULL, segundo_apellido VARCHAR(255) DEFAULT NULL, correo VARCHAR(255) DEFAULT NULL, despacho VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE grupo ADD CONSTRAINT FK_8C0E9BD3C5C70C5B FOREIGN KEY (asignatura_id) REFERENCES asignatura (id)');
        $this->addSql('ALTER TABLE clase ADD asignatura_id INT NOT NULL, DROP asignatura');
        $this->addSql('ALTER TABLE clase ADD CONSTRAINT FK_199FACCEC5C70C5B FOREIGN KEY (asignatura_id) REFERENCES asignatura (id)');
        $this->addSql('CREATE INDEX IDX_199FACCEC5C70C5B ON clase (asignatura_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase DROP FOREIGN KEY FK_199FACCEC5C70C5B');
        $this->addSql('ALTER TABLE grupo DROP FOREIGN KEY FK_8C0E9BD3C5C70C5B');
        $this->addSql('DROP TABLE asignatura');
        $this->addSql('DROP TABLE grupo');
        $this->addSql('DROP TABLE profesor');
        $this->addSql('DROP INDEX IDX_199FACCEC5C70C5B ON clase');
        $this->addSql('ALTER TABLE clase ADD asignatura VARCHAR(255) DEFAULT NULL, DROP asignatura_id');
    }
}
