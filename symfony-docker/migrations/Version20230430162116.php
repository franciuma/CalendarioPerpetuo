<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230430162116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE festivo_centro (id INT AUTO_INCREMENT NOT NULL, centro_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, abreviatura VARCHAR(255) NOT NULL, inicio VARCHAR(255) NOT NULL, final VARCHAR(255) NOT NULL, INDEX IDX_4EFDBA04298137A7 (centro_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE festivo_centro ADD CONSTRAINT FK_4EFDBA04298137A7 FOREIGN KEY (centro_id) REFERENCES centro (id)');
        $this->addSql('ALTER TABLE evento ADD festivo_centro_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05BC6EA531 FOREIGN KEY (festivo_centro_id) REFERENCES festivo_centro (id)');
        $this->addSql('CREATE INDEX IDX_47860B05BC6EA531 ON evento (festivo_centro_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05BC6EA531');
        $this->addSql('ALTER TABLE festivo_centro DROP FOREIGN KEY FK_4EFDBA04298137A7');
        $this->addSql('DROP TABLE festivo_centro');
        $this->addSql('DROP INDEX IDX_47860B05BC6EA531 ON evento');
        $this->addSql('ALTER TABLE evento DROP festivo_centro_id');
    }
}
