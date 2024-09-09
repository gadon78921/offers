<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521160949 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IF NOT EXISTS offer_name_index_on_offers ON offers (name)');
        $this->addSql('CREATE INDEX IF NOT EXISTS product_name_index_on_products ON products (name)');

        $this->addSql('CREATE INDEX IF NOT EXISTS kladr_id_index_on_offers ON offers (kladr_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS kladr_id_index_on_products ON products (kladr_id)');

        $this->addSql('CREATE INDEX IF NOT EXISTS price_for_preorder_index_on_offers ON offers (price_for_preorder)');
        $this->addSql('CREATE INDEX IF NOT EXISTS price_for_preorder_index_on_products ON products (price_for_preorder)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS offer_name_index_on_offers');
        $this->addSql('DROP INDEX IF EXISTS product_name_index_on_products');

        $this->addSql('DROP INDEX IF EXISTS kladr_id_index_on_offers');
        $this->addSql('DROP INDEX IF EXISTS kladr_id_index_on_products');

        $this->addSql('DROP INDEX IF EXISTS price_for_preorder_index_on_offers');
        $this->addSql('DROP INDEX IF EXISTS price_for_preorder_index_on_products');
    }
}
