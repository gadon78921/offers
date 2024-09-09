<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240628130755 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE retail_stocks ADD COLUMN updated_at timestamp(0) with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('CREATE TABLE yandex_products (kais_product_id BIGINT NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE yandex_products');
        $this->addSql('ALTER TABLE retail_stocks DROP COLUMN updated_at');
    }
}
