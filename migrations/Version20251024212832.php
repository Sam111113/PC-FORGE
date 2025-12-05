<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024212832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boitier CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE build CHANGE total_price total_price NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE cooler CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE cpu CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE fan CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE gpu CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE motherboard CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE psu CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE ram CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE storage CHANGE prix prix DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boitier CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE build CHANGE total_price total_price INT NOT NULL');
        $this->addSql('ALTER TABLE cooler CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE cpu CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE fan CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE gpu CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE motherboard CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE psu CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE ram CHANGE prix prix NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE storage CHANGE prix prix NUMERIC(8, 2) NOT NULL');
    }
}
