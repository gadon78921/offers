<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516144012 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offers ADD COLUMN IF NOT EXISTS category_ids int[] DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD COLUMN IF NOT EXISTS category_ids int[] DEFAULT NULL');
        $this->addSql('CREATE INDEX category_ids_index_on_offers ON offers (category_ids)');
        $this->addSql('CREATE INDEX category_ids_index_on_products ON products (category_ids)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offers DROP COLUMN IF EXISTS category_ids');
        $this->addSql('ALTER TABLE products DROP COLUMN IF EXISTS category_ids');
        $this->addSql('DROP INDEX category_ids_index_on_offers');
        $this->addSql('DROP INDEX category_ids_index_on_products');
    }
}
