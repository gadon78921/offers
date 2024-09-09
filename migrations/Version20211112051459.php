<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112051459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE retail_assortment_units (assortment_unit_id bigint, assortment_category_id INT DEFAULT NULL, PRIMARY KEY(assortment_unit_id))');
        $this->addSql('CREATE TABLE kais_products (kais_product_id bigint, PRIMARY KEY(kais_product_id))');
        $this->addSql('
            CREATE TABLE retail_products (
                retail_product_id bigint,
                kais_product_id bigint NOT NULL,
                assortment_unit_id bigint NOT NULL,
                PRIMARY KEY(retail_product_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_stocks (
                kais_product_id bigint,
                firm_subdivision_id varchar (255) NOT NULL,
                store_id bigint NOT NULL,
                free_qty integer NOT NULL DEFAULT 0,
                divided_free_qty integer NOT NULL DEFAULT 0,
                retail_price_with_tax decimal(10,2) NOT NULL,
                avg_income_price_with_tax decimal(10,2) NOT NULL,
                dti timestamp(0) with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(kais_product_id, firm_subdivision_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_cities (
                retail_city_id bigint,
                kladr_id varchar(13) NOT NULL,
                PRIMARY KEY(retail_city_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_tradepoints (
                trade_point_id bigint,
                kladr_id varchar(13) DEFAULT NULL,
                delivery_available boolean NOT NULL DEFAULT \'false\',
                firm_list_ids varchar[] DEFAULT ARRAY[]::VARCHAR[],
                supplier_list_ids varchar[] DEFAULT ARRAY[]::VARCHAR[],
                PRIMARY KEY(trade_point_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_prices_from_city (
                assortment_unit_id bigint,
                price decimal(10,2) NOT NULL,
                price_for_preorder decimal(10,2) NOT NULL,
                discount_for_preorder smallint NOT NULL,
                price_for_waiting decimal(10,2) NOT NULL,
                discount_for_waiting smallint NOT NULL,
                is_fixed_discount boolean NOT NULL
            )
        ');

        $this->addSql('
            CREATE TABLE retail_prices (
                assortment_unit_id bigint,
                kladr_id varchar(13),
                price decimal(10,2) NOT NULL,
                price_for_preorder decimal(10,2) NOT NULL,
                discount_for_preorder smallint NOT NULL,
                price_for_waiting decimal(10,2) NOT NULL,
                discount_for_waiting smallint NOT NULL,
                is_fixed_discount boolean NOT NULL,
                PRIMARY KEY(assortment_unit_id, kladr_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_fixed_discounts (
                id bigint,
                assortment_unit_id bigint NOT NULL,
                region_id smallint DEFAULT NULL,
                discount smallint NOT NULL,
                date_from timestamp(0) with time zone NOT NULL,
                date_to timestamp(0) with time zone DEFAULT NULL,
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_delivery_time_from_suppliers (
                rule_id bigint,
                region_id smallint,
                supplier_id bigint NOT NULL,
                firm_id bigint DEFAULT NULL,
                days_to_send_orders varchar[] DEFAULT ARRAY[]::VARCHAR[],
                order_send_time time without time zone NOT NULL,
                hours_until_ready smallint NOT NULL,
                date_from timestamp(0) with time zone NOT NULL,
                PRIMARY KEY(rule_id, region_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_suppliers (
                supplier_id bigint,
                supplier_code bigint NOT NULL,
                PRIMARY KEY(supplier_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_supplier_prices (
                kais_product_id bigint,
                supplier_id bigint,
                supplier_price decimal(10,2) NOT NULL,
                quantity int NOT NULL,
                PRIMARY KEY(kais_product_id, supplier_id)
            )
        ');

        $this->addSql('
            CREATE TABLE retail_supplier_prices_from_supplier (
                supplier_product_id bigint,
                retail_product_id bigint,
                kais_product_id bigint,
                supplier_price decimal(10,2) NOT NULL,
                cost decimal(10,2),
                multiplicity smallint,
                min_qty int,
                quantity int NOT NULL,
                best_before timestamp(0) without time zone NOT NULL,
                correct_best_before smallint,
                producer_price decimal(10,2),
                registry_price decimal(10,2),
                is_gnvls_problem boolean,
                is_for_tz_only boolean NOT NULL
            )
        ');

        $this->addSql('
            CREATE TABLE retail_supplier_priorities_region (
                retail_product_id bigint,
                trade_point_id bigint,
                supplier_list_ids varchar
            )
        ');

        $this->addSql('
            CREATE TABLE retail_supplier_priorities (
                retail_product_id bigint,
                trade_point_id bigint,
                supplier_list_ids varchar[] DEFAULT ARRAY[]::VARCHAR[],
                region_id smallint NOT NULL,
                PRIMARY KEY(retail_product_id, trade_point_id)
            )
        ');
        $this->addSql('CREATE INDEX region_id_index_on_retail_supplier_priorities ON retail_supplier_priorities (region_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE retail_assortment_units');
        $this->addSql('DROP TABLE kais_products');
        $this->addSql('DROP TABLE retail_products');
        $this->addSql('DROP TABLE retail_stocks');
        $this->addSql('DROP TABLE retail_cities');
        $this->addSql('DROP TABLE retail_tradepoints');
        $this->addSql('DROP TABLE retail_prices_from_city');
        $this->addSql('DROP TABLE retail_prices');
        $this->addSql('DROP TABLE retail_fixed_discounts');
        $this->addSql('DROP TABLE retail_delivery_time_from_suppliers');
        $this->addSql('DROP TABLE retail_suppliers');
        $this->addSql('DROP TABLE retail_supplier_prices');
        $this->addSql('DROP TABLE retail_supplier_prices_from_supplier');
        $this->addSql('DROP INDEX region_id_index_on_retail_supplier_priorities');
        $this->addSql('DROP TABLE retail_supplier_priorities_region');
        $this->addSql('DROP TABLE retail_supplier_priorities');
    }
}
