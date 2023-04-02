<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327103110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evento RENAME INDEX fk_47860b05c7f27734 TO IDX_47860B05C7F27734');
        $this->addSql('ALTER TABLE evento RENAME INDEX fk_47860b05d8235c9 TO IDX_47860B05D8235C9');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evento RENAME INDEX idx_47860b05c7f27734 TO FK_47860B05C7F27734');
        $this->addSql('ALTER TABLE evento RENAME INDEX idx_47860b05d8235c9 TO FK_47860B05D8235C9');
    }
}
