<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200619115607 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, entreprise_concernee_id INT NOT NULL, nom_destinataire VARCHAR(100) NOT NULL, prenom_destinataire VARCHAR(100) NOT NULL, email_destinaire VARCHAR(150) NOT NULL, date_envoi_enquete DATETIME DEFAULT NULL, date_reponse_enquete DATETIME DEFAULT NULL, statut_avis VARCHAR(100) DEFAULT NULL, ip_destinataire VARCHAR(80) DEFAULT NULL, token_security VARCHAR(255) DEFAULT NULL, note_prestation_realisee INT DEFAULT NULL, note_professionnalisme_entreprise INT DEFAULT NULL, note_satisfaction_globale INT DEFAULT NULL, recommander_commentaire_a_entreprise LONGTEXT DEFAULT NULL, temoignage_video VARCHAR(20) DEFAULT NULL, telephone_destinataire VARCHAR(20) DEFAULT NULL, INDEX IDX_8F91ABF0828ABE6E (entreprise_concernee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0828ABE6E FOREIGN KEY (entreprise_concernee_id) REFERENCES entreprise (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE avis');
    }
}
