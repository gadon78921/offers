<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221012145117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE retail_rule_delivery_time (
                rule_id bigint,
                region_id smallint,
                supplier_id bigint NOT NULL,
                trade_point_id bigint DEFAULT NULL,
                is_for_tz_only boolean NOT NULL,
                date_from timestamp(0) with time zone NOT NULL,
                PRIMARY KEY(rule_id, region_id)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE retail_rule_delivery_time');
    }
}
