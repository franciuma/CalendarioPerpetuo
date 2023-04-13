<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413122051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profesor_grupo (id INT AUTO_INCREMENT NOT NULL, grupo_id INT DEFAULT NULL, profesor_id INT DEFAULT NULL, INDEX IDX_38F339929C833003 (grupo_id), INDEX IDX_38F33992E52BD977 (profesor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profesor_grupo ADD CONSTRAINT FK_38F339929C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id)');
        $this->addSql('ALTER TABLE profesor_grupo ADD CONSTRAINT FK_38F33992E52BD977 FOREIGN KEY (profesor_id) REFERENCES profesor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profesor_grupo DROP FOREIGN KEY FK_38F339929C833003');
        $this->addSql('ALTER TABLE profesor_grupo DROP FOREIGN KEY FK_38F33992E52BD977');
        $this->addSql('DROP TABLE profesor_grupo');
    }
}
