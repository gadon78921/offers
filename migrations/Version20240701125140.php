<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240701125140 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE filters_meta_info ADD COLUMN IF NOT EXISTS is_available_for_fast_access bool DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE filters_meta_info DROP COLUMN IF EXISTS is_available_for_fast_access');
    }
}
