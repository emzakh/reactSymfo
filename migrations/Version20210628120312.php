<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210628120312 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaires (id INT AUTO_INCREMENT NOT NULL, recette_id INT DEFAULT NULL, author_id INT DEFAULT NULL, contenu LONGTEXT NOT NULL, rating INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_D9BEC0C489312FE9 (recette_id), INDEX IDX_D9BEC0C4F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, categorie VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, nomlatin VARCHAR(255) DEFAULT NULL, effets VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits_recettes (produits_id INT NOT NULL, recettes_id INT NOT NULL, INDEX IDX_147E1FFECD11A2CF (produits_id), INDEX IDX_147E1FFE3E2ED6D6 (recettes_id), PRIMARY KEY(produits_id, recettes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recettes (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, date DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, etapes LONGTEXT NOT NULL, types VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, img_recette VARCHAR(255) DEFAULT NULL, INDEX IDX_EB48E72CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, presentation LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C489312FE9 FOREIGN KEY (recette_id) REFERENCES recettes (id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produits_recettes ADD CONSTRAINT FK_147E1FFECD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produits_recettes ADD CONSTRAINT FK_147E1FFE3E2ED6D6 FOREIGN KEY (recettes_id) REFERENCES recettes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recettes ADD CONSTRAINT FK_EB48E72CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits_recettes DROP FOREIGN KEY FK_147E1FFECD11A2CF');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C489312FE9');
        $this->addSql('ALTER TABLE produits_recettes DROP FOREIGN KEY FK_147E1FFE3E2ED6D6');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4F675F31B');
        $this->addSql('ALTER TABLE recettes DROP FOREIGN KEY FK_EB48E72CF675F31B');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE produits_recettes');
        $this->addSql('DROP TABLE recettes');
        $this->addSql('DROP TABLE user');
    }
}
