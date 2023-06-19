<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619091117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario_grupo DROP FOREIGN KEY FK_91D0F1CD9C833003');
        $this->addSql('ALTER TABLE usuario_grupo CHANGE grupo_id grupo_id INT NOT NULL');
        $this->addSql('ALTER TABLE usuario_grupo ADD CONSTRAINT FK_91D0F1CD9C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario_grupo DROP FOREIGN KEY FK_91D0F1CD9C833003');
        $this->addSql('ALTER TABLE usuario_grupo CHANGE grupo_id grupo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario_grupo ADD CONSTRAINT FK_91D0F1CD9C833003 FOREIGN KEY (grupo_id) REFERENCES grupo (id)');
    }
}
