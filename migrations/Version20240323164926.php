<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323164926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expense_member (expense_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_B64B2B6DF395DB7B (expense_id), INDEX IDX_B64B2B6D7597D3FE (member_id), PRIMARY KEY(expense_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense_member ADD CONSTRAINT FK_B64B2B6DF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_member ADD CONSTRAINT FK_B64B2B6D7597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense ADD added_by_id INT NOT NULL, ADD paid_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA655B127A4 FOREIGN KEY (added_by_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA67F9BC654 FOREIGN KEY (paid_by_id) REFERENCES `member` (id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA655B127A4 ON expense (added_by_id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA67F9BC654 ON expense (paid_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense_member DROP FOREIGN KEY FK_B64B2B6DF395DB7B');
        $this->addSql('ALTER TABLE expense_member DROP FOREIGN KEY FK_B64B2B6D7597D3FE');
        $this->addSql('DROP TABLE expense_member');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA655B127A4');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA67F9BC654');
        $this->addSql('DROP INDEX IDX_2D3A8DA655B127A4 ON expense');
        $this->addSql('DROP INDEX IDX_2D3A8DA67F9BC654 ON expense');
        $this->addSql('ALTER TABLE expense DROP added_by_id, DROP paid_by_id');
    }
}
