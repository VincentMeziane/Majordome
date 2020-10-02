<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200920151419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add CreatedAt and UpdatedAt fields to Cards table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cards ADD created_at DATE DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATE DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cards DROP created_at, DROP updated_at');
    }
}
