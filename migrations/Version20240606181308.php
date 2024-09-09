<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606181308 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products ADD COLUMN IF NOT EXISTS generic_id int DEFAULT NULL');
        $this->addSql('ALTER TABLE kais_products ADD COLUMN IF NOT EXISTS full_trade_name varchar(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products DROP COLUMN IF EXISTS generic_id');
        $this->addSql('ALTER TABLE kais_products DROP COLUMN IF EXISTS full_trade_name');
    }
}
