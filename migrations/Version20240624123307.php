<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240624123307 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS filters (
             assortment_unit_id bigint,
             properties jsonb,
             PRIMARY KEY (assortment_unit_id)
        )');

        $this->addSql('CREATE TABLE IF NOT EXISTS filters_meta_info (
             filter_name varchar(255),
             type varchar(255) NOT NULL,
             item_type varchar(255) NOT NULL,
             title varchar(255) NOT NULL,
             view_type varchar(255) NOT NULL,
             gender varchar(255) NOT NULL,
             PRIMARY KEY (filter_name)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS filters');
        $this->addSql('DROP TABLE IF EXISTS filters_meta_info');
    }
}
