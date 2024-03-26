<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323161744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B995E70BCD7');
        $this->addSql('DROP INDEX UNIQ_51A53B995E70BCD7 ON splitter');
        $this->addSql('ALTER TABLE splitter DROP owned_by_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter ADD owned_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B995E70BCD7 FOREIGN KEY (owned_by_id) REFERENCES `member` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51A53B995E70BCD7 ON splitter (owned_by_id)');
    }
}
