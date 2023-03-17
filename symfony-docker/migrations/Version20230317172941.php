<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230317172941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anio ADD calendario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE anio ADD CONSTRAINT FK_89A50577A7F6EA19 FOREIGN KEY (calendario_id) REFERENCES calendario (id)');
        $this->addSql('CREATE INDEX IDX_89A50577A7F6EA19 ON anio (calendario_id)');
        $this->addSql('ALTER TABLE calendario DROP anio');
        $this->addSql('ALTER TABLE clase ADD abreviatura VARCHAR(255) NOT NULL, CHANGE lugar aula VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE dia CHANGE valor num_dia VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_DEF6205B3A909126 ON festivo_nacional');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anio DROP FOREIGN KEY FK_89A50577A7F6EA19');
        $this->addSql('DROP INDEX IDX_89A50577A7F6EA19 ON anio');
        $this->addSql('ALTER TABLE anio DROP calendario_id');
        $this->addSql('ALTER TABLE calendario ADD anio VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE clase ADD lugar VARCHAR(255) NOT NULL, DROP aula, DROP abreviatura');
        $this->addSql('ALTER TABLE dia CHANGE num_dia valor VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DEF6205B3A909126 ON festivo_nacional (nombre)');
    }
}
