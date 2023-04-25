<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425095039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendario ADD comienzo_de_clases VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE profesor DROP comienzo_de_clases');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendario DROP comienzo_de_clases');
        $this->addSql('ALTER TABLE profesor ADD comienzo_de_clases VARCHAR(255) NOT NULL');
    }
}
