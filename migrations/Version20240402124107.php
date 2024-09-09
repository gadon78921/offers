<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402124107 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE retail_rule_delivery_time');
        $this->addSql('
            CREATE TABLE retail_rule_delivery_time (
                head_id bigint,
                rule_id bigint,
                region_id smallint,
                supplier_id bigint NOT NULL,
                trade_point_id bigint DEFAULT NULL,
                is_for_tz_only boolean NOT NULL,
                date_from timestamp(0) with time zone NOT NULL,
                PRIMARY KEY(head_id, rule_id, region_id)
            )
        ');

        $this->addSql('DROP TABLE retail_rule_tradepoint_params');
        $this->addSql('
            CREATE TABLE retail_rule_tradepoint_params (
                head_id         bigint,
                rule_id         bigint,
                trade_point_id  bigint NOT NULL,
                work_start_hour smallint NOT NULL,
                work_end_hour   smallint NOT NULL,
                days_of_work    varchar(64) NOT NULL,
                date_from       timestamp(0) with time zone NOT NULL,
                PRIMARY KEY(head_id, rule_id, trade_point_id)
            )
        ');

        $this->addSql('DROP TABLE retail_delivery_time_from_suppliers');
        $this->addSql('
            CREATE TABLE retail_delivery_time_from_suppliers (
                head_id bigint,
                rule_id bigint,
                region_id smallint,
                supplier_id bigint NOT NULL,
                firm_id bigint DEFAULT NULL,
                days_to_send_orders varchar[] DEFAULT ARRAY[]::VARCHAR[],
                order_send_time time without time zone NOT NULL,
                hours_until_ready smallint NOT NULL,
                date_from timestamp(0) with time zone NOT NULL,
                PRIMARY KEY(head_id, rule_id, region_id)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE retail_rule_delivery_time');
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

        $this->addSql('DROP TABLE retail_rule_tradepoint_params');
        $this->addSql('
            CREATE TABLE retail_rule_tradepoint_params (
                rule_id         bigint,
                trade_point_id  bigint NOT NULL,
                work_start_hour smallint NOT NULL,
                work_end_hour   smallint NOT NULL,
                days_of_work    varchar(64) NOT NULL,
                date_from       timestamp(0) with time zone NOT NULL,
                PRIMARY KEY(rule_id, trade_point_id)
            )
        ');

        $this->addSql('DROP TABLE retail_delivery_time_from_suppliers');
        $this->addSql('
            CREATE TABLE retail_delivery_time_from_suppliers (
                rule_id bigint,
                region_id smallint,
                supplier_id bigint NOT NULL,
                firm_id bigint DEFAULT NULL,
                days_to_send_orders varchar[] DEFAULT ARRAY[]::VARCHAR[],
                order_send_time time without time zone NOT NULL,
                hours_until_ready smallint NOT NULL,
                date_from timestamp(0) with time zone NOT NULL,
                PRIMARY KEY(rule_id, region_id)
            )
        ');
    }
}
