<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220217112856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD categorie VARCHAR(255) NOT NULL, CHANGE modele_vehicule_id modele_vehicule_id INT DEFAULT NULL');
        //$this->addSql('ALTER TABLE annonce RENAME INDEX fk_f65593e58003a08c TO IDX_F65593E58003A08C');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP categorie, CHANGE modele_vehicule_id modele_vehicule_id INT NOT NULL, CHANGE titre titre VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE contenu contenu VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE annonce RENAME INDEX idx_f65593e58003a08c TO FK_F65593E58003A08C');
        $this->addSql('ALTER TABLE automobile CHANGE marque marque VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modele modele VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
