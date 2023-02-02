<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202074505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B9912469DE2 FOREIGN KEY (category_id) REFERENCES splitter_category (id)');
        $this->addSql('CREATE INDEX IDX_51A53B9912469DE2 ON splitter (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B9912469DE2');
        $this->addSql('DROP INDEX IDX_51A53B9912469DE2 ON splitter');
        $this->addSql('ALTER TABLE splitter DROP category_id');
    }
}
