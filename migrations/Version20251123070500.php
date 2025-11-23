<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to create transfer table for money transfers between splitter members
 */
final class Version20251123070500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create transfer table for money transfers between splitter members';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, splitter_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', from_member_id INT NOT NULL, to_member_id INT NOT NULL, added_by_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, made_at DATE NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_4034A3C0C8EE9C17 (splitter_id), INDEX IDX_4034A3C0C18DED7A (from_member_id), INDEX IDX_4034A3C0E8BA82F3 (to_member_id), INDEX IDX_4034A3C055B127A4 (added_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0C8EE9C17 FOREIGN KEY (splitter_id) REFERENCES splitter (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0C18DED7A FOREIGN KEY (from_member_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C0E8BA82F3 FOREIGN KEY (to_member_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C055B127A4 FOREIGN KEY (added_by_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0C8EE9C17');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0C18DED7A');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C0E8BA82F3');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C055B127A4');
        $this->addSql('DROP TABLE transfer');
    }
}
