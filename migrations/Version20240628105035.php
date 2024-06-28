<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240628105035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_location_price (id INT AUTO_INCREMENT NOT NULL, state VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, delivery_fee VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_delivery_details (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, recipient_phone_number VARCHAR(255) NOT NULL, recipient_address VARCHAR(500) NOT NULL, category VARCHAR(255) NOT NULL, land_mark VARCHAR(255) DEFAULT NULL, area VARCHAR(255) DEFAULT NULL, apartment_number VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_52DF689A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_delivery_details ADD CONSTRAINT FK_52DF689A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE delivery_details DROP FOREIGN KEY FK_7838B45412136921');
        $this->addSql('DROP TABLE delivery_location_details');
        $this->addSql('DROP TABLE delivery_details');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_location_details (id INT AUTO_INCREMENT NOT NULL, recipient_phone_number VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, recipient_address VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, land_mark VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, area VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, apartment_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE delivery_details (id INT AUTO_INCREMENT NOT NULL, delivery_id INT DEFAULT NULL, delivery_fee VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, state VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, location VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_7838B45412136921 (delivery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE delivery_details ADD CONSTRAINT FK_7838B45412136921 FOREIGN KEY (delivery_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_delivery_details DROP FOREIGN KEY FK_52DF689A76ED395');
        $this->addSql('DROP TABLE delivery_location_price');
        $this->addSql('DROP TABLE user_delivery_details');
    }
}
