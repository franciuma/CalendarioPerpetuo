<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230524152151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase ADD grupo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE clase ADD CONSTRAINT FK_199FACCE9C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id)');
        $this->addSql('CREATE INDEX IDX_199FACCE9C833003 ON clase (grupo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase DROP FOREIGN KEY FK_199FACCE9C833003');
        $this->addSql('DROP INDEX IDX_199FACCE9C833003 ON clase');
        $this->addSql('ALTER TABLE clase DROP grupo_id');
    }
}
