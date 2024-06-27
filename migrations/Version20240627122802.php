<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627122802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_details DROP start_time, DROP end_time, DROP status');
        $this->addSql('ALTER TABLE order_details ADD start_time DATETIME NOT NULL, ADD end_time DATETIME DEFAULT NULL, ADD order_status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details DROP start_time, DROP end_time, DROP order_status');
        $this->addSql('ALTER TABLE delivery_details ADD start_time DATETIME NOT NULL, ADD end_time DATETIME DEFAULT NULL, ADD status VARCHAR(255) NOT NULL');
    }
}
