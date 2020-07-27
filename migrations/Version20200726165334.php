<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200726165334 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fiche_prospection (id INT AUTO_INCREMENT NOT NULL, responsable_fiche_prospection_id INT DEFAULT NULL, nom_entreprise VARCHAR(200) NOT NULL, raison_sociale_entreprise VARCHAR(150) NOT NULL, code_ape VARCHAR(80) DEFAULT NULL, rue_entreprise VARCHAR(255) DEFAULT NULL, ville_entreprise VARCHAR(150) DEFAULT NULL, code_postal_entreprise VARCHAR(20) DEFAULT NULL, telephone_fixe_entreprise VARCHAR(40) DEFAULT NULL, telephone_portable_entreprise VARCHAR(40) DEFAULT NULL, email_entreprise VARCHAR(255) DEFAULT NULL, date_derniere_modification DATETIME DEFAULT NULL, siret_siren VARCHAR(200) DEFAULT NULL, date_creation DATETIME DEFAULT NULL, statut VARCHAR(50) DEFAULT NULL, INDEX IDX_649CE00B14A09EDE (responsable_fiche_prospection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_personnalise (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fiche_prospection ADD CONSTRAINT FK_649CE00B14A09EDE FOREIGN KEY (responsable_fiche_prospection_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entreprise ADD ape VARCHAR(200) DEFAULT NULL, ADD siren_siret VARCHAR(255) DEFAULT NULL, ADD slug VARCHAR(200) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fiche_prospection');
        $this->addSql('DROP TABLE message_personnalise');
        $this->addSql('ALTER TABLE entreprise DROP ape, DROP siren_siret, DROP slug');
    }
}
