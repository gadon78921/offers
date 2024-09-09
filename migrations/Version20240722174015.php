<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240722174015 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE yandex_products ADD CONSTRAINT yandex_products_pk PRIMARY KEY(kais_product_id)');
        $this->addSql('
            ALTER TABLE yandex_products
                ADD COLUMN IF NOT EXISTS image_url varchar(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS image_hash varchar(255) DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE yandex_products
                DROP COLUMN IF EXISTS image_url,
                DROP COLUMN IF EXISTS image_hash
        ');
        $this->addSql('ALTER TABLE yandex_products DROP CONSTRAINT yandex_products_pk');
    }
}
