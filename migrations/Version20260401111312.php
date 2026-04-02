<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401111312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add expense_share table and split_type column (with safe backfill for production data)';
    }

    public function up(Schema $schema): void
    {
        // 1. Create the expense_share table
        $this->addSql('CREATE TABLE expense_share (id INT AUTO_INCREMENT NOT NULL, expense_id INT NOT NULL, member_id INT NOT NULL, share DOUBLE PRECISION NOT NULL, INDEX IDX_4C0E3A60F395DB7B (expense_id), INDEX IDX_4C0E3A607597D3FE (member_id), UNIQUE INDEX uniq_expense_share_member (expense_id, member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense_share ADD CONSTRAINT FK_4C0E3A60F395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_share ADD CONSTRAINT FK_4C0E3A607597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id)');

        // 2. Add split_type as NULLABLE first (safe for existing rows)
        $this->addSql('ALTER TABLE expense ADD split_type VARCHAR(20) DEFAULT NULL');

        // 3. Backfill all existing expenses with the default "equal" split type
        $this->addSql("UPDATE expense SET split_type = 'equal' WHERE split_type IS NULL");

        // 4. Now enforce NOT NULL
        $this->addSql('ALTER TABLE expense MODIFY split_type VARCHAR(20) NOT NULL');

        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense_share DROP FOREIGN KEY FK_4C0E3A60F395DB7B');
        $this->addSql('ALTER TABLE expense_share DROP FOREIGN KEY FK_4C0E3A607597D3FE');
        $this->addSql('DROP TABLE expense_share');
        $this->addSql('ALTER TABLE expense DROP split_type');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }
}
