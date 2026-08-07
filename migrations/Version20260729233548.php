<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260729233548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, mois INT NOT NULL, annee INT NOT NULL, montant_total DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, date_validation DATETIME DEFAULT NULL, projet_id INT NOT NULL, INDEX IDX_FE866410C18272 (projet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_facture (id INT AUTO_INCREMENT NOT NULL, ressource VARCHAR(255) NOT NULL, quantite_reelle DOUBLE PRECISION NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, unite VARCHAR(255) NOT NULL, montant_ligne DOUBLE PRECISION NOT NULL, facture_id INT NOT NULL, INDEX IDX_611F5A297F2DEE08 (facture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_offre (id INT AUTO_INCREMENT NOT NULL, ressource VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, unite VARCHAR(255) NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, type_service VARCHAR(255) DEFAULT NULL, offre_financiere_id INT NOT NULL, INDEX IDX_AC2A6C5F2ACF0050 (offre_financiere_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offre_financiere (id INT AUTO_INCREMENT NOT NULL, date_import DATETIME NOT NULL, fichier_source VARCHAR(255) DEFAULT NULL, version INT NOT NULL, active TINYINT NOT NULL, projet_id INT NOT NULL, INDEX IDX_CEC2E515C18272 (projet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, numero_so VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, statut VARCHAR(255) NOT NULL, societe_id INT NOT NULL, INDEX IDX_50159CA9FCF77503 (societe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, informations_generales LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE ligne_facture ADD CONSTRAINT FK_611F5A297F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE ligne_offre ADD CONSTRAINT FK_AC2A6C5F2ACF0050 FOREIGN KEY (offre_financiere_id) REFERENCES offre_financiere (id)');
        $this->addSql('ALTER TABLE offre_financiere ADD CONSTRAINT FK_CEC2E515C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410C18272');
        $this->addSql('ALTER TABLE ligne_facture DROP FOREIGN KEY FK_611F5A297F2DEE08');
        $this->addSql('ALTER TABLE ligne_offre DROP FOREIGN KEY FK_AC2A6C5F2ACF0050');
        $this->addSql('ALTER TABLE offre_financiere DROP FOREIGN KEY FK_CEC2E515C18272');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9FCF77503');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE ligne_facture');
        $this->addSql('DROP TABLE ligne_offre');
        $this->addSql('DROP TABLE offre_financiere');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE societe');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
