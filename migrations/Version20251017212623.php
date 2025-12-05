<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251017212623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boitier (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, length INT NOT NULL, heigth INT NOT NULL, width INT NOT NULL, gpu_max_l INT NOT NULL, fan_slot INT NOT NULL, psu_max_l INT NOT NULL, aio_support INT NOT NULL, mb_form_factor VARCHAR(30) NOT NULL, fan_slot_width INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE build (id INT AUTO_INCREMENT NOT NULL, motherboard_id INT DEFAULT NULL, cpu_id INT DEFAULT NULL, boitier_id INT DEFAULT NULL, psu_id INT DEFAULT NULL, cooler_id INT DEFAULT NULL, image_id INT DEFAULT NULL, user_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_BDA0F2DB6511E8A3 (motherboard_id), INDEX IDX_BDA0F2DB3917014 (cpu_id), INDEX IDX_BDA0F2DB44DE6F7C (boitier_id), INDEX IDX_BDA0F2DBC1737AF1 (psu_id), INDEX IDX_BDA0F2DB810064FD (cooler_id), UNIQUE INDEX UNIQ_BDA0F2DB3DA5256D (image_id), INDEX IDX_BDA0F2DBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE build_gpu (build_id INT NOT NULL, gpu_id INT NOT NULL, INDEX IDX_AC200BF617C13F8B (build_id), INDEX IDX_AC200BF698003202 (gpu_id), PRIMARY KEY(build_id, gpu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE build_ram (build_id INT NOT NULL, ram_id INT NOT NULL, INDEX IDX_F678D12B17C13F8B (build_id), INDEX IDX_F678D12B3366068 (ram_id), PRIMARY KEY(build_id, ram_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE build_storage (build_id INT NOT NULL, storage_id INT NOT NULL, INDEX IDX_79EF66B817C13F8B (build_id), INDEX IDX_79EF66B85CC5DB90 (storage_id), PRIMARY KEY(build_id, storage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE build_fan (build_id INT NOT NULL, fan_id INT NOT NULL, INDEX IDX_745E8B3D17C13F8B (build_id), INDEX IDX_745E8B3D89C48F0B (fan_id), PRIMARY KEY(build_id, fan_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cooler (id INT AUTO_INCREMENT NOT NULL, is_aio TINYINT(1) NOT NULL, heigth INT NOT NULL, tdp INT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, nb_fan INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cpu (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, socket VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fan (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, quantity INT NOT NULL, width INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gpu (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, pcie_module VARCHAR(30) NOT NULL, length INT NOT NULL, tdp INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motherboard (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, socket VARCHAR(20) NOT NULL, pcie_slot INT NOT NULL, pcie_module VARCHAR(20) NOT NULL, slot_m2 INT NOT NULL, sata_port INT NOT NULL, memory_max INT NOT NULL, memory_type VARCHAR(30) NOT NULL, memory_slot INT NOT NULL, form_factor VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, titre LONGTEXT NOT NULL, accroche LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1DD399503DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE psu (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, wattage INT NOT NULL, pcie_connector INT NOT NULL, eps_connector INT NOT NULL, sata_connector INT NOT NULL, modularite VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ram (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, type VARCHAR(30) NOT NULL, total INT NOT NULL, nb_module INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(30) NOT NULL, interface VARCHAR(30) NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(100) NOT NULL, prix NUMERIC(8, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DB6511E8A3 FOREIGN KEY (motherboard_id) REFERENCES motherboard (id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DB3917014 FOREIGN KEY (cpu_id) REFERENCES cpu (id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DB44DE6F7C FOREIGN KEY (boitier_id) REFERENCES boitier (id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DBC1737AF1 FOREIGN KEY (psu_id) REFERENCES psu (id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DB810064FD FOREIGN KEY (cooler_id) REFERENCES cooler (id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DB3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE build ADD CONSTRAINT FK_BDA0F2DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE build_gpu ADD CONSTRAINT FK_AC200BF617C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE build_gpu ADD CONSTRAINT FK_AC200BF698003202 FOREIGN KEY (gpu_id) REFERENCES gpu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE build_ram ADD CONSTRAINT FK_F678D12B17C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE build_ram ADD CONSTRAINT FK_F678D12B3366068 FOREIGN KEY (ram_id) REFERENCES ram (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE build_storage ADD CONSTRAINT FK_79EF66B817C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE build_storage ADD CONSTRAINT FK_79EF66B85CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE build_fan ADD CONSTRAINT FK_745E8B3D17C13F8B FOREIGN KEY (build_id) REFERENCES build (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE build_fan ADD CONSTRAINT FK_745E8B3D89C48F0B FOREIGN KEY (fan_id) REFERENCES fan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD399503DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE build DROP FOREIGN KEY FK_BDA0F2DB6511E8A3');
        $this->addSql('ALTER TABLE build DROP FOREIGN KEY FK_BDA0F2DB3917014');
        $this->addSql('ALTER TABLE build DROP FOREIGN KEY FK_BDA0F2DB44DE6F7C');
        $this->addSql('ALTER TABLE build DROP FOREIGN KEY FK_BDA0F2DBC1737AF1');
        $this->addSql('ALTER TABLE build DROP FOREIGN KEY FK_BDA0F2DB810064FD');
        $this->addSql('ALTER TABLE build DROP FOREIGN KEY FK_BDA0F2DB3DA5256D');
        $this->addSql('ALTER TABLE build DROP FOREIGN KEY FK_BDA0F2DBA76ED395');
        $this->addSql('ALTER TABLE build_gpu DROP FOREIGN KEY FK_AC200BF617C13F8B');
        $this->addSql('ALTER TABLE build_gpu DROP FOREIGN KEY FK_AC200BF698003202');
        $this->addSql('ALTER TABLE build_ram DROP FOREIGN KEY FK_F678D12B17C13F8B');
        $this->addSql('ALTER TABLE build_ram DROP FOREIGN KEY FK_F678D12B3366068');
        $this->addSql('ALTER TABLE build_storage DROP FOREIGN KEY FK_79EF66B817C13F8B');
        $this->addSql('ALTER TABLE build_storage DROP FOREIGN KEY FK_79EF66B85CC5DB90');
        $this->addSql('ALTER TABLE build_fan DROP FOREIGN KEY FK_745E8B3D17C13F8B');
        $this->addSql('ALTER TABLE build_fan DROP FOREIGN KEY FK_745E8B3D89C48F0B');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD399503DA5256D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493DA5256D');
        $this->addSql('DROP TABLE boitier');
        $this->addSql('DROP TABLE build');
        $this->addSql('DROP TABLE build_gpu');
        $this->addSql('DROP TABLE build_ram');
        $this->addSql('DROP TABLE build_storage');
        $this->addSql('DROP TABLE build_fan');
        $this->addSql('DROP TABLE cooler');
        $this->addSql('DROP TABLE cpu');
        $this->addSql('DROP TABLE fan');
        $this->addSql('DROP TABLE gpu');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE motherboard');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE psu');
        $this->addSql('DROP TABLE ram');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
