<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323162300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B997E3C61F9 FOREIGN KEY (owner_id) REFERENCES `member` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51A53B997E3C61F9 ON splitter (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B997E3C61F9');
        $this->addSql('DROP INDEX UNIQ_51A53B997E3C61F9 ON splitter');
        $this->addSql('ALTER TABLE splitter DROP owner_id');
    }
}
