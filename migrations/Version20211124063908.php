<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211124063908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE offers (
                assortment_unit_id           bigint,
                tradepoint_id                bigint,
                kladr_id                     varchar(13) NOT NULL,
                kais_product_id              bigint,
                price_without_discount       numeric(10, 2) DEFAULT 0.0,
                price_for_preorder           numeric(10, 2) DEFAULT 0.0,
                price_for_waiting            numeric(10, 2) DEFAULT 0.0,
                discount_for_preorder        smallint NOT NULL DEFAULT 0,
                discount_for_waiting         smallint NOT NULL DEFAULT 0,
                quantity_in_storage          smallint NOT NULL DEFAULT 0,
                quantity_in_storage_unpacked smallint NOT NULL DEFAULT 0,
                quantity_from_suppliers      smallint NOT NULL DEFAULT 0,
                supplier_ids                 bigint[] DEFAULT NULL,
                PRIMARY KEY (assortment_unit_id, tradepoint_id)
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE offers');
    }
}
