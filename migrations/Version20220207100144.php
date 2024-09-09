<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207100144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offers ADD COLUMN wholesale_price numeric(10, 2) DEFAULT 0.0');
        $this->addSql('ALTER TABLE products ADD COLUMN wholesale_price numeric(10, 2) DEFAULT 0.0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offers DROP COLUMN wholesale_price');
        $this->addSql('ALTER TABLE products DROP COLUMN wholesale_price');
    }
}
