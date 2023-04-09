<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409175515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase ADD calendario_id INT NOT NULL');
        $this->addSql('ALTER TABLE clase ADD CONSTRAINT FK_199FACCEA7F6EA19 FOREIGN KEY (calendario_id) REFERENCES calendario (id)');
        $this->addSql('CREATE INDEX IDX_199FACCEA7F6EA19 ON clase (calendario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase DROP FOREIGN KEY FK_199FACCEA7F6EA19');
        $this->addSql('DROP INDEX IDX_199FACCEA7F6EA19 ON clase');
        $this->addSql('ALTER TABLE clase DROP calendario_id');
    }
}
