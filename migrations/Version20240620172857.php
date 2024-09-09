<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240620172857 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products ADD weight DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE kais_products ADD length INT DEFAULT  NULL');
        $this->addSql('ALTER TABLE kais_products ADD width INT DEFAULT  NULL');
        $this->addSql('ALTER TABLE kais_products ADD height INT DEFAULT  NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kais_products DROP weight');
        $this->addSql('ALTER TABLE kais_products DROP length');
        $this->addSql('ALTER TABLE kais_products DROP width');
        $this->addSql('ALTER TABLE kais_products DROP height');
    }
}
