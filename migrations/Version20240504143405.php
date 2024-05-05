<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240504143405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter DROP INDEX UNIQ_51A53B997E3C61F9, ADD INDEX IDX_51A53B997E3C61F9 (owner_id)');
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B997E3C61F9');
        $this->addSql('ALTER TABLE splitter CHANGE owner_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B997E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter DROP INDEX IDX_51A53B997E3C61F9, ADD UNIQUE INDEX UNIQ_51A53B997E3C61F9 (owner_id)');
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B997E3C61F9');
        $this->addSql('ALTER TABLE splitter CHANGE owner_id owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B997E3C61F9 FOREIGN KEY (owner_id) REFERENCES `member` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
