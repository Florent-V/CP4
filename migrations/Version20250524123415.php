<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250524123415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE app_user_member (id INT AUTO_INCREMENT NOT NULL, app_user_id INT NOT NULL, splitter_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', member_id INT NOT NULL, INDEX IDX_4C53C7F94A3353D8 (app_user_id), INDEX IDX_4C53C7F9E8DFE41 (splitter_id), INDEX IDX_4C53C7F97597D3FE (member_id), UNIQUE INDEX unique_selection (app_user_id, splitter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user_member ADD CONSTRAINT FK_4C53C7F94A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user_member ADD CONSTRAINT FK_4C53C7F9E8DFE41 FOREIGN KEY (splitter_id) REFERENCES splitter (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user_member ADD CONSTRAINT FK_4C53C7F97597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user_member DROP FOREIGN KEY FK_4C53C7F94A3353D8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user_member DROP FOREIGN KEY FK_4C53C7F9E8DFE41
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user_member DROP FOREIGN KEY FK_4C53C7F97597D3FE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE app_user_member
        SQL);
    }
}
