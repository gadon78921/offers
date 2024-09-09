<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415150909 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE retail_products ADD COLUMN retail_product_code bigint NOT NULL');
        $this->addSql('ALTER TABLE kais_products ADD COLUMN kais_product_code bigint NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE retail_products DROP COLUMN retail_product_code');
        $this->addSql('ALTER TABLE kais_products DROP COLUMN kais_product_code');
    }
}
