<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018162535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag ADD task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7838DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_389B7838DB60186 ON tag (task_id)');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258D7B4FB4');
        $this->addSql('DROP INDEX IDX_527EDB258D7B4FB4 ON task');
        $this->addSql('ALTER TABLE task DROP tags_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7838DB60186');
        $this->addSql('DROP INDEX IDX_389B7838DB60186 ON tag');
        $this->addSql('ALTER TABLE tag DROP task_id');
        $this->addSql('ALTER TABLE task ADD tags_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_527EDB258D7B4FB4 ON task (tags_id)');
    }
}
