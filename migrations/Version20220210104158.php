<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210104158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5726B3660');
        $this->addSql('ALTER TABLE annonce DROP marque_vehicule_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD marque_vehicule_id INT NOT NULL, CHANGE titre titre VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE contenu contenu VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5726B3660 FOREIGN KEY (marque_vehicule_id) REFERENCES automobile (id)');
        $this->addSql('CREATE INDEX FK_F65593E5726B3660 ON annonce (marque_vehicule_id)');
        $this->addSql('ALTER TABLE annonce RENAME INDEX idx_f65593e58003a08c TO FK_F65593E58003A08C');
        $this->addSql('ALTER TABLE automobile CHANGE marque marque VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modele modele VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
