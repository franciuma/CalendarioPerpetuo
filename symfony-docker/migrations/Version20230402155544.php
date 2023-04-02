<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402155544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX numAnio_idx ON anio (num_anio)');
        $this->addSql('CREATE INDEX nombre_idx ON calendario (nombre)');
        $this->addSql('CREATE INDEX provincia_idx ON calendario (provincia)');
        $this->addSql('ALTER TABLE festivo_local RENAME INDEX inicio_idx TO inicio_local_idx');
        $this->addSql('CREATE INDEX inicio_nacional_idx ON festivo_nacional (inicio)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX numAnio_idx ON anio');
        $this->addSql('DROP INDEX nombre_idx ON calendario');
        $this->addSql('DROP INDEX provincia_idx ON calendario');
        $this->addSql('ALTER TABLE festivo_local RENAME INDEX inicio_local_idx TO inicio_idx');
        $this->addSql('DROP INDEX inicio_nacional_idx ON festivo_nacional');
    }
}
