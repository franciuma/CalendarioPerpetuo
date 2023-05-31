<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230531153030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE titulacion DROP INDEX UNIQ_873C1824298137A7, ADD INDEX IDX_873C1824298137A7 (centro_id)');
        $this->addSql('ALTER TABLE titulacion CHANGE centro_id centro_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE titulacion DROP INDEX IDX_873C1824298137A7, ADD UNIQUE INDEX UNIQ_873C1824298137A7 (centro_id)');
        $this->addSql('ALTER TABLE titulacion CHANGE centro_id centro_id INT DEFAULT NULL');
    }
}
