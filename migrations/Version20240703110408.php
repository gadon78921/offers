<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240703110408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление справочников товаров по аналогии с сервисом поиска';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE kais_products
                ADD COLUMN IF NOT EXISTS name VARCHAR(255),
                ADD COLUMN IF NOT EXISTS is_gnvls BOOLEAN,
                ADD COLUMN IF NOT EXISTS producer_id INT,
                ADD COLUMN IF NOT EXISTS storage_conditions TEXT DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS storage_type VARCHAR(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS sell_procedure VARCHAR(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS actual_sell_procedure VARCHAR(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS full_additional_name VARCHAR(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS person_category_id INT DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS person_category_suffix_id INT DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS full_form_name VARCHAR(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS full_first_wrapping_name VARCHAR(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS first_packing SMALLINT DEFAULT NUll,
                ADD COLUMN IF NOT EXISTS second_packing SMALLINT DEFAULT NUll,
                ADD COLUMN IF NOT EXISTS suffix VARCHAR(255) DEFAULT NULL
        ');
        $this->addSql('CREATE TABLE IF NOT EXISTS kais_producers (id INT NOT NULL, name VARCHAR(255) NOT NULL, kais_country_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS kais_countries (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS kais_product_person_categories (id INT NOT NULL, deleted BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS kais_product_person_category_suffixes (id INT NOT NULL, deleted BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS dont_show_one_pack (name VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE kais_products
                DROP COLUMN IF EXISTS name,
                DROP COLUMN IF EXISTS is_gnvls,
                DROP COLUMN IF EXISTS producer_id,
                DROP COLUMN IF EXISTS storage_conditions,
                DROP COLUMN IF EXISTS storage_type,
                DROP COLUMN IF EXISTS sell_procedure,
                DROP COLUMN IF EXISTS actual_sell_procedure,
                DROP COLUMN IF EXISTS full_additional_name,
                DROP COLUMN IF EXISTS person_category_id,
                DROP COLUMN IF EXISTS person_category_suffix_id,
                DROP COLUMN IF EXISTS full_form_name,
                DROP COLUMN IF EXISTS full_first_wrapping_name,
                DROP COLUMN IF EXISTS first_packing,
                DROP COLUMN IF EXISTS second_packing,
                DROP COLUMN IF EXISTS suffix
        ');
        $this->addSql('DROP TABLE IF EXISTS kais_producers');
        $this->addSql('DROP TABLE IF EXISTS kais_countries');
        $this->addSql('DROP TABLE IF EXISTS kais_product_person_categories');
        $this->addSql('DROP TABLE IF EXISTS kais_product_person_category_suffixes');
        $this->addSql('DROP TABLE IF EXISTS dont_show_one_pack');
    }
}
