<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426222622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservationeven ADD nom_client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservationeven ADD CONSTRAINT FK_D7292B578D1A1860 FOREIGN KEY (nom_client_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D7292B578D1A1860 ON reservationeven (nom_client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservationeven DROP FOREIGN KEY FK_D7292B578D1A1860');
        $this->addSql('DROP INDEX IDX_D7292B578D1A1860 ON reservationeven');
        $this->addSql('ALTER TABLE reservationeven DROP nom_client_id');
    }
}
