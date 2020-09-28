<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200928084438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return "Ajout d'une clé étrangère entre Cards et Users";
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cards ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE cards ADD CONSTRAINT FK_4C258FDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4C258FDA76ED395 ON cards (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cards DROP FOREIGN KEY FK_4C258FDA76ED395');
        $this->addSql('DROP INDEX IDX_4C258FDA76ED395 ON cards');
        $this->addSql('ALTER TABLE cards DROP user_id');
    }
}
