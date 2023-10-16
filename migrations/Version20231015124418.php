<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231015124418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE splitter_user (splitter_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id INT NOT NULL, INDEX IDX_57996F0DE8DFE41 (splitter_id), INDEX IDX_57996F0DA76ED395 (user_id), PRIMARY KEY(splitter_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE splitter_user ADD CONSTRAINT FK_57996F0DE8DFE41 FOREIGN KEY (splitter_id) REFERENCES splitter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE splitter_user ADD CONSTRAINT FK_57996F0DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `member` ADD editor TINYINT(1) NOT NULL, CHANGE user_id user_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE splitter_user DROP FOREIGN KEY FK_57996F0DE8DFE41');
        $this->addSql('ALTER TABLE splitter_user DROP FOREIGN KEY FK_57996F0DA76ED395');
        $this->addSql('DROP TABLE splitter_user');
        $this->addSql('ALTER TABLE `member` DROP editor, CHANGE user_id user_id INT NOT NULL');
    }
}
