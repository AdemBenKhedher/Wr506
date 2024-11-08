<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014064538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_object (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movie ADD media_id INT DEFAULT NULL, DROP media');
        $this->addSql('ALTER TABLE movie ADD CONSTRAINT FK_1D5EF26FEA9FDD75 FOREIGN KEY (media_id) REFERENCES media_object (id)');
        $this->addSql('CREATE INDEX IDX_1D5EF26FEA9FDD75 ON movie (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie DROP FOREIGN KEY FK_1D5EF26FEA9FDD75');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP INDEX IDX_1D5EF26FEA9FDD75 ON movie');
        $this->addSql('ALTER TABLE movie ADD media VARCHAR(255) DEFAULT NULL, DROP media_id');
    }
}
