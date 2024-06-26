<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240626142700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_7838B4548D9F6D38 ON delivery_details');
        $this->addSql('ALTER TABLE delivery_details ADD state VARCHAR(255) NOT NULL, ADD location VARCHAR(255) NOT NULL, DROP order_id');
        $this->addSql('ALTER TABLE order_details ADD state VARCHAR(255) NOT NULL, ADD location VARCHAR(255) NOT NULL, DROP area');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details ADD area VARCHAR(255) DEFAULT NULL, DROP state, DROP location');
        $this->addSql('ALTER TABLE delivery_details ADD order_id INT NOT NULL, DROP state, DROP location');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7838B4548D9F6D38 ON delivery_details (order_id)');
    }
}
