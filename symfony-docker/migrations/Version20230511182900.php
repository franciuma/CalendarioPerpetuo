<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511182900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendario DROP FOREIGN KEY FK_2F19AB8CE52BD977');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, primer_apellido VARCHAR(255) DEFAULT NULL, segundo_apellido VARCHAR(255) DEFAULT NULL, correo VARCHAR(255) DEFAULT NULL, tipo VARCHAR(255) NOT NULL, despacho VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario_grupo (id INT AUTO_INCREMENT NOT NULL, grupo_id INT DEFAULT NULL, usuario_id INT DEFAULT NULL, INDEX IDX_91D0F1CD9C833003 (grupo_id), INDEX IDX_91D0F1CDDB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usuario_grupo ADD CONSTRAINT FK_91D0F1CD9C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id)');
        $this->addSql('ALTER TABLE usuario_grupo ADD CONSTRAINT FK_91D0F1CDDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE profesor_grupo DROP FOREIGN KEY FK_38F33992E52BD977');
        $this->addSql('ALTER TABLE profesor_grupo DROP FOREIGN KEY FK_38F339929C833003');
        $this->addSql('DROP TABLE profesor');
        $this->addSql('DROP TABLE profesor_grupo');
        $this->addSql('DROP INDEX UNIQ_2F19AB8CE52BD977 ON calendario');
        $this->addSql('ALTER TABLE calendario CHANGE profesor_id usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendario ADD CONSTRAINT FK_2F19AB8CDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F19AB8CDB38439E ON calendario (usuario_id)');
        $this->addSql('ALTER TABLE titulacion ADD centro_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE titulacion ADD CONSTRAINT FK_873C1824298137A7 FOREIGN KEY (centro_id) REFERENCES centro (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_873C1824298137A7 ON titulacion (centro_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendario DROP FOREIGN KEY FK_2F19AB8CDB38439E');
        $this->addSql('CREATE TABLE profesor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, primer_apellido VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, segundo_apellido VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, correo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, despacho VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE profesor_grupo (id INT AUTO_INCREMENT NOT NULL, grupo_id INT DEFAULT NULL, profesor_id INT DEFAULT NULL, INDEX IDX_38F339929C833003 (grupo_id), INDEX IDX_38F33992E52BD977 (profesor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE profesor_grupo ADD CONSTRAINT FK_38F33992E52BD977 FOREIGN KEY (profesor_id) REFERENCES profesor (id)');
        $this->addSql('ALTER TABLE profesor_grupo ADD CONSTRAINT FK_38F339929C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id)');
        $this->addSql('ALTER TABLE usuario_grupo DROP FOREIGN KEY FK_91D0F1CD9C833003');
        $this->addSql('ALTER TABLE usuario_grupo DROP FOREIGN KEY FK_91D0F1CDDB38439E');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE usuario_grupo');
        $this->addSql('DROP INDEX UNIQ_2F19AB8CDB38439E ON calendario');
        $this->addSql('ALTER TABLE calendario CHANGE usuario_id profesor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendario ADD CONSTRAINT FK_2F19AB8CE52BD977 FOREIGN KEY (profesor_id) REFERENCES profesor (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F19AB8CE52BD977 ON calendario (profesor_id)');
        $this->addSql('ALTER TABLE titulacion DROP FOREIGN KEY FK_873C1824298137A7');
        $this->addSql('DROP INDEX UNIQ_873C1824298137A7 ON titulacion');
        $this->addSql('ALTER TABLE titulacion DROP centro_id');
    }
}
