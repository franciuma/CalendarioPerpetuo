<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325133045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE festivo_local (id INT AUTO_INCREMENT NOT NULL, calendario_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, abreviatura VARCHAR(255) NOT NULL, inicio VARCHAR(255) NOT NULL, final VARCHAR(255) NOT NULL, INDEX IDX_DEDC1F3AA7F6EA19 (calendario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE festivo_local ADD CONSTRAINT FK_DEDC1F3AA7F6EA19 FOREIGN KEY (calendario_id) REFERENCES calendario (id)');
        $this->addSql('ALTER TABLE calendario ADD provincia VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE festivo_local DROP FOREIGN KEY FK_DEDC1F3AA7F6EA19');
        $this->addSql('DROP TABLE festivo_local');
        $this->addSql('ALTER TABLE calendario DROP provincia');
    }
}
