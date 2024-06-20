<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620123821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_details (id INT AUTO_INCREMENT NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, order_id INT NOT NULL, UNIQUE INDEX UNIQ_7838B4548D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE delivery_location_details (id INT AUTO_INCREMENT NOT NULL, recipient_phone_number VARCHAR(255) NOT NULL, recipient_address VARCHAR(500) NOT NULL, category VARCHAR(255) NOT NULL, land_mark VARCHAR(255) DEFAULT NULL, area VARCHAR(255) DEFAULT NULL, apartment_number VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, customer_name VARCHAR(255) NOT NULL, pickup_contact_no VARCHAR(255) NOT NULL, pickup_address VARCHAR(500) NOT NULL, land_mark VARCHAR(255) DEFAULT NULL, area VARCHAR(255) DEFAULT NULL, apartment_number VARCHAR(255) DEFAULT NULL, rider_id INT NOT NULL, delivery_details_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_845CA2C1FF881F6 (rider_id), UNIQUE INDEX UNIQ_845CA2C1A55F9006 (delivery_details_id), INDEX IDX_845CA2C1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rider_details (id INT AUTO_INCREMENT NOT NULL, rider_name VARCHAR(255) NOT NULL, rider_contact_no VARCHAR(255) NOT NULL, vehicle_details VARCHAR(255) NOT NULL, current_location VARCHAR(255) DEFAULT NULL, banned_untill DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, confirmed TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_profile (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, profile_image VARCHAR(255) DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_D95AB405A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE delivery_details ADD CONSTRAINT FK_7838B4548D9F6D38 FOREIGN KEY (order_id) REFERENCES order_details (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1FF881F6 FOREIGN KEY (rider_id) REFERENCES rider_details (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1A55F9006 FOREIGN KEY (delivery_details_id) REFERENCES delivery_details (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_details DROP FOREIGN KEY FK_7838B4548D9F6D38');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1FF881F6');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1A55F9006');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1A76ED395');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405A76ED395');
        $this->addSql('DROP TABLE delivery_details');
        $this->addSql('DROP TABLE delivery_location_details');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('DROP TABLE rider_details');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_profile');
    }
}
