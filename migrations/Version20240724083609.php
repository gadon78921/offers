<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240724083609 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products ADD COLUMN IF NOT EXISTS kais_gtins varchar[] DEFAULT array[]::varchar[]');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products DROP COLUMN IF EXISTS kais_gtins');
    }
}
