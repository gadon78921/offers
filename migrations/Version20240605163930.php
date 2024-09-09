<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605163930 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products ADD COLUMN IF NOT EXISTS substitute_kais_product_ids bigint[] DEFAULT array[]::bigint[]');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products DROP COLUMN IF EXISTS substitute_kais_product_ids');
    }
}
