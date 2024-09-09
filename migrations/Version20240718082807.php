<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240718082807 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS kais_generic (id int NOT NULL, name varchar(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS kais_simplified_dosage_form (id int NOT NULL, name varchar(255) NOT NULL, routeOfAdministration varchar(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS category_seo_attributes (category_id int, products_title_n varchar(255) DEFAULT NULL, products_descr_n varchar(255) DEFAULT NULL, PRIMARY KEY (category_id))');

        $this->addSql('
            ALTER TABLE kais_products
                ADD COLUMN IF NOT EXISTS indications text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS contraindications text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS pharmacodynamics text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS pharmacokinetics text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS side_effects text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS dosage_and_administration text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS ingredients text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS description text DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS expiration_date int DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS simplified_dosage_form_id int DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS packing varchar(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS dose varchar(255) DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS kais_generic');
        $this->addSql('DROP TABLE IF EXISTS kais_simplified_dosage_form');
        $this->addSql('DROP TABLE IF EXISTS category_seo_attributes');

        $this->addSql('
            ALTER TABLE kais_products
                DROP COLUMN IF EXISTS indications,
                DROP COLUMN IF EXISTS contraindications,
                DROP COLUMN IF EXISTS pharmacodynamics,
                DROP COLUMN IF EXISTS pharmacokinetics,
                DROP COLUMN IF EXISTS side_effects,
                DROP COLUMN IF EXISTS dosage_and_administration,
                DROP COLUMN IF EXISTS ingredients,
                DROP COLUMN IF EXISTS description,
                DROP COLUMN IF EXISTS expiration_date,
                DROP COLUMN IF EXISTS simplified_dosage_form_id,
                DROP COLUMN IF EXISTS packing,
                DROP COLUMN IF EXISTS dose
        ');
    }
}
