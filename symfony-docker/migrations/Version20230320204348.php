<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320204348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dia ADD evento_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dia ADD CONSTRAINT FK_3E153BCE87A5F842 FOREIGN KEY (evento_id) REFERENCES festivo_nacional (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3E153BCE87A5F842 ON dia (evento_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dia DROP FOREIGN KEY FK_3E153BCE87A5F842');
        $this->addSql('DROP INDEX UNIQ_3E153BCE87A5F842 ON dia');
        $this->addSql('ALTER TABLE dia DROP evento_id');
    }
}
