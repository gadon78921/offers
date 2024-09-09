<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221012131852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE retail_fixed_discounts');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE retail_fixed_discounts (
                id bigint,
                assortment_unit_id bigint NOT NULL,
                region_id smallint DEFAULT NULL,
                discount smallint NOT NULL,
                date_from timestamp(0) with time zone NOT NULL,
                date_to timestamp(0) with time zone DEFAULT NULL,
                PRIMARY KEY(id)
            )
        ');
    }
}
