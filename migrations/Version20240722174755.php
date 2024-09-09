<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240722174755 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE yandex_tradepoints (tradepoint_id BIGINT NOT NULL, PRIMARY KEY(tradepoint_id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE yandex_tradepoints');
    }
}
