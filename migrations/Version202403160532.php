<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202403160532 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE retail_supplier_prices_from_supplier ALTER COLUMN supplier_product_id TYPE varchar(255)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE retail_supplier_prices_from_supplier ALTER COLUMN supplier_product_id TYPE bigint');
    }
}
