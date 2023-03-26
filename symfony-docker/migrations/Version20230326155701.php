<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326155701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evento (id INT AUTO_INCREMENT NOT NULL, festivo_nacional_id INT DEFAULT NULL, festivo_local_id INT DEFAULT NULL, dia_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_47860B05C7F27734 (festivo_nacional_id), UNIQUE INDEX UNIQ_47860B05D8235C9 (festivo_local_id), UNIQUE INDEX UNIQ_47860B05AC1F7597 (dia_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05C7F27734 FOREIGN KEY (festivo_nacional_id) REFERENCES festivo_nacional (id)');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05D8235C9 FOREIGN KEY (festivo_local_id) REFERENCES festivo_local (id)');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B05AC1F7597 FOREIGN KEY (dia_id) REFERENCES dia (id)');
        $this->addSql('ALTER TABLE dia DROP FOREIGN KEY FK_3E153BCE87A5F842');
        $this->addSql('ALTER TABLE dia DROP FOREIGN KEY FK_3E153BCE9DFCD864');
        $this->addSql('DROP INDEX UNIQ_3E153BCE9DFCD864 ON dia');
        $this->addSql('DROP INDEX UNIQ_3E153BCE87A5F842 ON dia');
        $this->addSql('ALTER TABLE dia DROP evento_id, DROP evento_local_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05C7F27734');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05D8235C9');
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B05AC1F7597');
        $this->addSql('DROP TABLE evento');
        $this->addSql('ALTER TABLE dia ADD evento_id INT DEFAULT NULL, ADD evento_local_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dia ADD CONSTRAINT FK_3E153BCE87A5F842 FOREIGN KEY (evento_id) REFERENCES festivo_nacional (id)');
        $this->addSql('ALTER TABLE dia ADD CONSTRAINT FK_3E153BCE9DFCD864 FOREIGN KEY (evento_local_id) REFERENCES festivo_local (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3E153BCE9DFCD864 ON dia (evento_local_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3E153BCE87A5F842 ON dia (evento_id)');
    }
}
