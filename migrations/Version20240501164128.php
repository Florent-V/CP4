<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240501164128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_88BDF3E9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_user_splitter (app_user_id INT NOT NULL, splitter_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2B55E2354A3353D8 (app_user_id), INDEX IDX_2B55E235E8DFE41 (splitter_id), PRIMARY KEY(app_user_id, splitter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE app_user_splitter ADD CONSTRAINT FK_2B55E2354A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_user_splitter ADD CONSTRAINT FK_2B55E235E8DFE41 FOREIGN KEY (splitter_id) REFERENCES splitter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_category DROP FOREIGN KEY FK_C02DDB3855B127A4');
        $this->addSql('DROP INDEX IDX_C02DDB3855B127A4 ON expense_category');
        $this->addSql('ALTER TABLE expense_category DROP added_by_id');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78A76ED395');
        $this->addSql('DROP INDEX IDX_70E4FA78A76ED395 ON `member`');
        $this->addSql('ALTER TABLE `member` DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E9A76ED395');
        $this->addSql('ALTER TABLE app_user_splitter DROP FOREIGN KEY FK_2B55E2354A3353D8');
        $this->addSql('ALTER TABLE app_user_splitter DROP FOREIGN KEY FK_2B55E235E8DFE41');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE app_user_splitter');
        $this->addSql('ALTER TABLE `member` ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_70E4FA78A76ED395 ON `member` (user_id)');
        $this->addSql('ALTER TABLE expense_category ADD added_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE expense_category ADD CONSTRAINT FK_C02DDB3855B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C02DDB3855B127A4 ON expense_category (added_by_id)');
    }
}
