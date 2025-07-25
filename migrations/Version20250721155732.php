<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721155732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE share_code (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, code VARCHAR(6) NOT NULL, entity_type VARCHAR(100) NOT NULL, entity_id VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, is_used TINYINT(1) DEFAULT 0 NOT NULL, used_at DATETIME DEFAULT NULL, used_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CAD23D0177153098 (code), INDEX IDX_CAD23D01DE12AB56 (created_by), INDEX IDX_CAD23D0116FE72E1 (updated_by), INDEX idx_share_code (code), INDEX idx_entity (entity_type, entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE share_code ADD CONSTRAINT FK_CAD23D01DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE share_code ADD CONSTRAINT FK_CAD23D0116FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE share_code DROP FOREIGN KEY FK_CAD23D01DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE share_code DROP FOREIGN KEY FK_CAD23D0116FE72E1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE share_code
        SQL);
    }
}
