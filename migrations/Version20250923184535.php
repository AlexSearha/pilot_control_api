<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923184535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item_project DROP FOREIGN KEY FK_24EEF6BA126F525E');
        $this->addSql('ALTER TABLE item_project DROP FOREIGN KEY FK_24EEF6BA166D1F9C');
        $this->addSql('DROP TABLE item_project');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_project (item_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_24EEF6BA126F525E (item_id), INDEX IDX_24EEF6BA166D1F9C (project_id), PRIMARY KEY(item_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE item_project ADD CONSTRAINT FK_24EEF6BA126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_project ADD CONSTRAINT FK_24EEF6BA166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
