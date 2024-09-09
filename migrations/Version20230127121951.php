<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127121951 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
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
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE retail_rule_tradepoint_params');
    }
}
