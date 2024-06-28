<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240628084326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_details ADD delivery_id INT DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE delivery_details ADD CONSTRAINT FK_7838B45412136921 FOREIGN KEY (delivery_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7838B45412136921 ON delivery_details (delivery_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_details DROP FOREIGN KEY FK_7838B45412136921');
        $this->addSql('DROP INDEX UNIQ_7838B45412136921 ON delivery_details');
        $this->addSql('ALTER TABLE delivery_details ADD user_id INT NOT NULL, DROP delivery_id');
    }
}
