<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406181425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anio (id INT AUTO_INCREMENT NOT NULL, calendario_id INT DEFAULT NULL, num_anio VARCHAR(255) NOT NULL, INDEX IDX_89A50577A7F6EA19 (calendario_id), INDEX numAnio_idx (num_anio), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calendario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, provincia VARCHAR(255) NOT NULL, INDEX nombre_idx (nombre), INDEX provincia_idx (provincia), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clase (id INT AUTO_INCREMENT NOT NULL, aula VARCHAR(255) DEFAULT NULL, correo VARCHAR(255) DEFAULT NULL, asignatura VARCHAR(255) DEFAULT NULL, tipo_de_clase VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dia (id INT AUTO_INCREMENT NOT NULL, mes_id INT DEFAULT NULL, num_dia VARCHAR(255) NOT NULL, es_no_lectivo TINYINT(1) NOT NULL, fecha VARCHAR(255) NOT NULL, nombre_dia_de_la_semana VARCHAR(255) NOT NULL, INDEX IDX_3E153BCEB4F0564A (mes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evento (id INT AUTO_INCREMENT NOT NULL, dia_id INT DEFAULT NULL, festivo_nacional_id INT DEFAULT NULL, festivo_local_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_47860B05AC1F7597 (dia_id), INDEX IDX_47860B05C7F27734 (festivo_nacional_id), INDEX IDX_47860B05D8235C9 (festivo_local_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE festivo_local (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, abreviatura VARCHAR(255) NOT NULL, inicio VARCHAR(255) NOT NULL, final VARCHAR(255) NOT NULL, provincia VARCHAR(255) NOT NULL, INDEX inicio_local_idx (inicio), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE festivo_nacional (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, abreviatura VARCHAR(255) NOT NULL, inicio VARCHAR(255) NOT NULL, final VARCHAR(255) NOT NULL, INDEX inicio_nacional_idx (inicio), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mes (id INT AUTO_INCREMENT NOT NULL, anio_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, num_mes VARCHAR(255) NOT NULL, primer_dia INT NOT NULL, INDEX IDX_6EC83E05EC34184E (anio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE anio ADD CONSTRAINT FK_89A50577A7F6EA19 FOREIGN KEY (calendario_id) REFERENCES calendario (id)');
        $this->addSql('ALTER TABLE dia ADD CONSTRAINT FK_3E153BCEB4F0564A FOREIGN KEY (mes_id) REFERENCES mes (id)');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05AC1F7597 FOREIGN KEY (dia_id) REFERENCES dia (id)');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05C7F27734 FOREIGN KEY (festivo_nacional_id) REFERENCES festivo_nacional (id)');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05D8235C9 FOREIGN KEY (festivo_local_id) REFERENCES festivo_local (id)');
        $this->addSql('ALTER TABLE mes ADD CONSTRAINT FK_6EC83E05EC34184E FOREIGN KEY (anio_id) REFERENCES anio (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anio DROP FOREIGN KEY FK_89A50577A7F6EA19');
        $this->addSql('ALTER TABLE dia DROP FOREIGN KEY FK_3E153BCEB4F0564A');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05AC1F7597');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05C7F27734');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05D8235C9');
        $this->addSql('ALTER TABLE mes DROP FOREIGN KEY FK_6EC83E05EC34184E');
        $this->addSql('DROP TABLE anio');
        $this->addSql('DROP TABLE calendario');
        $this->addSql('DROP TABLE clase');
        $this->addSql('DROP TABLE dia');
        $this->addSql('DROP TABLE evento');
        $this->addSql('DROP TABLE festivo_local');
        $this->addSql('DROP TABLE festivo_nacional');
        $this->addSql('DROP TABLE mes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
