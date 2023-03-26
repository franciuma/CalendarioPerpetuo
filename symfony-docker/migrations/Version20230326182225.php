<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326182225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evento DROP INDEX UNIQ_47860B05C7F27734');
        $this->addSql('ALTER TABLE evento DROP INDEX UNIQ_47860B05D8235C9');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05C7F27734');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05D8235C9');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_47860B05C7F27734 ON evento (festivo_nacional_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_47860B05D8235C9 ON evento (festivo_local_id)');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05C7F27734 FOREIGN KEY (festivo_nacional_id) REFERENCES festivo_nacional (id)');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05D8235C9 FOREIGN KEY (festivo_local_id) REFERENCES festivo_local (id)');
    }
}
