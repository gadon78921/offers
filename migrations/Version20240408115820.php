<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408115820 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE retail_rule_delivery_time RENAME COLUMN trade_point_id TO firm_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE retail_rule_delivery_time RENAME COLUMN firm_id TO trade_point_id');
    }
}
