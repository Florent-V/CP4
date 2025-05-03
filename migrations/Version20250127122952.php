<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127122952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('ALTER TABLE expense ADD created_by INT DEFAULT NULL, ADD updated_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6DE12AB56 ON expense (created_by)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA616FE72E1 ON expense (updated_by)');
        $this->addSql('ALTER TABLE splitter ADD created_by INT DEFAULT NULL, ADD updated_by INT DEFAULT NULL, ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B99DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE splitter ADD CONSTRAINT FK_51A53B9916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_51A53B99DE12AB56 ON splitter (created_by)');
        $this->addSql('CREATE INDEX IDX_51A53B9916FE72E1 ON splitter (updated_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B99DE12AB56');
        $this->addSql('ALTER TABLE splitter DROP FOREIGN KEY FK_51A53B9916FE72E1');
        $this->addSql('DROP INDEX IDX_51A53B99DE12AB56 ON splitter');
        $this->addSql('DROP INDEX IDX_51A53B9916FE72E1 ON splitter');
        $this->addSql('ALTER TABLE splitter DROP created_by, DROP updated_by, DROP created_at, DROP updated_at, DROP deleted_at');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6DE12AB56');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA616FE72E1');
        $this->addSql('DROP INDEX IDX_2D3A8DA6DE12AB56 ON expense');
        $this->addSql('DROP INDEX IDX_2D3A8DA616FE72E1 ON expense');
        $this->addSql('ALTER TABLE expense DROP created_by, DROP updated_by, DROP deleted_at, CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }
}
