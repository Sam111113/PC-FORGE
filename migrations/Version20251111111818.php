<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111111818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boitier ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE cooler ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE cpu ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE fan ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE gpu ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE motherboard ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE psu ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE ram ADD ascin VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE storage ADD ascin VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boitier DROP ascin');
        $this->addSql('ALTER TABLE cooler DROP ascin');
        $this->addSql('ALTER TABLE cpu DROP ascin');
        $this->addSql('ALTER TABLE fan DROP ascin');
        $this->addSql('ALTER TABLE gpu DROP ascin');
        $this->addSql('ALTER TABLE motherboard DROP ascin');
        $this->addSql('ALTER TABLE psu DROP ascin');
        $this->addSql('ALTER TABLE ram DROP ascin');
        $this->addSql('ALTER TABLE storage DROP ascin');
    }
}
