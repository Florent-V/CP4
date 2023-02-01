<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230201144409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expense_category (id INT AUTO_INCREMENT NOT NULL, added_by_id INT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(100) NOT NULL, INDEX IDX_C02DDB3855B127A4 (added_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE splitter_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense_category ADD CONSTRAINT FK_C02DDB3855B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C155B127A4');
        $this->addSql('DROP TABLE category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, added_by_id INT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_64C19C155B127A4 (added_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C155B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE expense_category DROP FOREIGN KEY FK_C02DDB3855B127A4');
        $this->addSql('DROP TABLE expense_category');
        $this->addSql('DROP TABLE splitter_category');
    }
}
