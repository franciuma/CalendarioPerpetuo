<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230408183328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase ADD fecha VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evento ADD clase_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B059F720353 FOREIGN KEY (clase_id) REFERENCES clase (id)');
        $this->addSql('CREATE INDEX IDX_47860B059F720353 ON evento (clase_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clase DROP fecha');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B059F720353');
        $this->addSql('DROP INDEX IDX_47860B059F720353 ON evento');
        $this->addSql('ALTER TABLE evento DROP clase_id');
    }
}
