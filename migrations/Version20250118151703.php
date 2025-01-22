<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118151703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter ADD created_by INT DEFAULT NULL, ADD updated_by INT DEFAULT NULL, ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B99DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B9916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_51A53B99DE12AB56 ON splitter (created_by)');
        $this->addSql('CREATE INDEX IDX_51A53B9916FE72E1 ON splitter (updated_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B99DE12AB56');
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B9916FE72E1');
        $this->addSql('DROP INDEX IDX_51A53B99DE12AB56 ON splitter');
        $this->addSql('DROP INDEX IDX_51A53B9916FE72E1 ON splitter');
        $this->addSql('ALTER TABLE splitter DROP created_by, DROP updated_by, DROP created_at, DROP updated_at, DROP deleted_at');
    }
}
