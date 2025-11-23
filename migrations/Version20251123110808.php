<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251123110808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, splitter_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', from_member_id INT NOT NULL, to_member_id INT NOT NULL, added_by_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, made_at DATE NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_4034A3C0E8DFE41 (splitter_id), INDEX IDX_4034A3C0650B4644 (from_member_id), INDEX IDX_4034A3C04434048F (to_member_id), INDEX IDX_4034A3C055B127A4 (added_by_id), INDEX IDX_4034A3C0DE12AB56 (created_by), INDEX IDX_4034A3C016FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0E8DFE41 FOREIGN KEY (splitter_id) REFERENCES splitter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0650B4644 FOREIGN KEY (from_member_id) REFERENCES `member` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C04434048F FOREIGN KEY (to_member_id) REFERENCES `member` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C055B127A4 FOREIGN KEY (added_by_id) REFERENCES app_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C016FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0E8DFE41
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0650B4644
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C04434048F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C055B127A4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0DE12AB56
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C016FE72E1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transfer
        SQL);
    }
}
