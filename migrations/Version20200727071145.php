<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200727071145 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_message_personnalise (user_id INT NOT NULL, message_personnalise_id INT NOT NULL, INDEX IDX_747C8343A76ED395 (user_id), INDEX IDX_747C8343E451A913 (message_personnalise_id), PRIMARY KEY(user_id, message_personnalise_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_message_personnalise ADD CONSTRAINT FK_747C8343A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_message_personnalise ADD CONSTRAINT FK_747C8343E451A913 FOREIGN KEY (message_personnalise_id) REFERENCES message_personnalise (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_message_personnalise');
    }
}
