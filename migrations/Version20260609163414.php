<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260609163414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device ALTER serial_number TYPE VARCHAR(50)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DEVICE_SERIAL_NUMBER ON device (serial_number)');
        $this->addSql('ALTER INDEX uniq_f244e948e7927c74 RENAME TO UNIQ_TECHNICIAN_EMAIL');
        $this->addSql('ALTER TABLE ticket ADD version INT DEFAULT 1 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USER_USERNAME ON "user" (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_DEVICE_SERIAL_NUMBER');
        $this->addSql('ALTER TABLE device ALTER serial_number TYPE VARCHAR(255)');
        $this->addSql('ALTER INDEX uniq_technician_email RENAME TO uniq_f244e948e7927c74');
        $this->addSql('ALTER TABLE ticket DROP version');
        $this->addSql('DROP INDEX UNIQ_USER_USERNAME');
    }
}
