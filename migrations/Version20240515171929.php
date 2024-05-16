<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515171929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, introduction VARCHAR(255) NOT NULL, INDEX IDX_BDAFD8C8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE author ADD CONSTRAINT FK_BDAFD8C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ad ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ad ADD CONSTRAINT FK_77E0ED58F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('CREATE INDEX IDX_77E0ED58F675F31B ON ad (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ad DROP FOREIGN KEY FK_77E0ED58F675F31B');
        $this->addSql('ALTER TABLE author DROP FOREIGN KEY FK_BDAFD8C8A76ED395');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP INDEX IDX_77E0ED58F675F31B ON ad');
        $this->addSql('ALTER TABLE ad ADD introduction LONGTEXT NOT NULL, DROP author_id');
    }
}
