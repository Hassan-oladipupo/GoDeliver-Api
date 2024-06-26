<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240625093242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add foreign key constraints';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('order_details')) {
            $this->addSql('CREATE TABLE order_details (
                orderId INT AUTO_INCREMENT NOT NULL,
                customerName VARCHAR(255) NOT NULL,
                pickupContactNo VARCHAR(255) NOT NULL,
                pickupAddress VARCHAR(500) NOT NULL,
                landMark VARCHAR(255) DEFAULT NULL,
                area VARCHAR(255) DEFAULT NULL,
                apartmentNumber VARCHAR(255) DEFAULT NULL,
                rider_id INT NOT NULL,
                user_id INT NOT NULL,
                PRIMARY KEY (orderId),
                CONSTRAINT FK_845CA2C1FF881F6 FOREIGN KEY (rider_id) REFERENCES rider_details (id),
                CONSTRAINT FK_845CA2C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        if (!$schema->hasTable('delivery_details')) {
            $this->addSql('CREATE TABLE delivery_details (
                id INT AUTO_INCREMENT NOT NULL,
                order_id INT NOT NULL,
                startTime DATETIME NOT NULL,
                endTime DATETIME DEFAULT NULL,
                status VARCHAR(255) NOT NULL,
                deliveryFee VARCHAR(255) NOT NULL,
                PRIMARY KEY (id),
                CONSTRAINT FK_7838B4548D9F6D38 FOREIGN KEY (order_id) REFERENCES order_details (orderId)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS delivery_details');

        $this->addSql('DROP TABLE IF EXISTS order_details');
    }
}
