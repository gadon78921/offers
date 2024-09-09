<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240722171833 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE kais_products 
                ADD COLUMN IF NOT EXISTS not_for_yandex_eda boolean NOT NULL DEFAULT false,
                ADD COLUMN updated_at timestamp(0) with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE kais_products 
                DROP COLUMN IF EXISTS not_for_yandex_eda,
                DROP COLUMN updated_at
        ');
    }
}
