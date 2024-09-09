<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419105724 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS products_groups_path (
                assortment_unit_id bigint,
                path jsonb NOT NULL,
                PRIMARY KEY (assortment_unit_id)
            )
        ');

        $this->addSql('ALTER TABLE offers ADD COLUMN name varchar(255) NOT NULL');
        $this->addSql('ALTER TABLE products ADD COLUMN name varchar(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products_groups_path');
        $this->addSql('ALTER TABLE offers DROP COLUMN name');
        $this->addSql('ALTER TABLE products DROP COLUMN name');
    }
}
