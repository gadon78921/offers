<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230201131535 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_products_assortment_unit_id ON products(assortment_unit_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_products_assortment_unit_id');
    }
}
