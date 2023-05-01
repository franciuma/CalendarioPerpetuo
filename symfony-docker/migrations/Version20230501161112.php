<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230501161112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendario ADD profesor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendario ADD CONSTRAINT FK_2F19AB8CE52BD977 FOREIGN KEY (profesor_id) REFERENCES profesor (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F19AB8CE52BD977 ON calendario (profesor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendario DROP FOREIGN KEY FK_2F19AB8CE52BD977');
        $this->addSql('DROP INDEX UNIQ_2F19AB8CE52BD977 ON calendario');
        $this->addSql('ALTER TABLE calendario DROP profesor_id');
    }
}
