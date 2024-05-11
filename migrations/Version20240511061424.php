<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511061424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA655B127A4');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA655B127A4 FOREIGN KEY (added_by_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA655B127A4');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA655B127A4 FOREIGN KEY (added_by_id) REFERENCES `member` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
