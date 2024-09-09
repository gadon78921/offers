<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411104236 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE assortment_unit_specifications (
                assortment_unit_id bigint,
                base_product_id bigint NOT NULL,
                package_quantity int NOT NULL,
                prescription_form smallint DEFAULT 0 NOT NULL,
                quantity_in_primary_pack int NOT NULL,
                quantity_in_secondary_pack int NOT NULL,
                is_vital boolean NOT NULL,
                is_storage_type_cold boolean NOT NULL,
                can_unpack_primary boolean NOT NULL,
                can_unpack_secondary boolean NOT NULL,
                full_product_name varchar(255) NOT NULL,
                product_name varchar(255) NOT NULL,
                subtitle varchar(255) NOT NULL,
                manufacturer_name varchar(255) NOT NULL,
                manufacturer_country_name varchar(255) NOT NULL,
                active_substance varchar(255) DEFAULT NULL,
                dosage varchar(255) DEFAULT NULL,
                dosage_form varchar(255) DEFAULT NULL,
                sell_procedure varchar(255) DEFAULT NULL,
                brand varchar(255) DEFAULT NULL,
                description text DEFAULT NULL,
                composition text DEFAULT NULL,
                indications_for_use text DEFAULT NULL,
                contraindications_for_use text DEFAULT NULL,
                pharmokinetic text DEFAULT NULL,
                pharmodynamic text DEFAULT NULL,
                method_of_use text DEFAULT NULL,
                side_effect text DEFAULT NULL,
                storage_conditions text DEFAULT NULL,
                wrapping_name varchar(255) DEFAULT NULL,
                expiration_date int DEFAULT NULL,
                PRIMARY KEY(assortment_unit_id)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE assortment_unit_specifications');
    }
}
