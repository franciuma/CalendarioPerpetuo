<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230615145322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grupo DROP FOREIGN KEY FK_8C0E9BD3C5C70C5B');
        $this->addSql('ALTER TABLE grupo ADD CONSTRAINT FK_8C0E9BD3C5C70C5B FOREIGN KEY (asignatura_id) REFERENCES asignatura (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grupo DROP FOREIGN KEY FK_8C0E9BD3C5C70C5B');
        $this->addSql('ALTER TABLE grupo ADD CONSTRAINT FK_8C0E9BD3C5C70C5B FOREIGN KEY (asignatura_id) REFERENCES asignatura (id)');
    }
}
