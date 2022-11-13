<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426214320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD even_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E8AB236CF FOREIGN KEY (even_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B26681E8AB236CF ON evenement (even_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E8AB236CF');
        $this->addSql('DROP INDEX IDX_B26681E8AB236CF ON evenement');
        $this->addSql('ALTER TABLE evenement DROP even_id');
    }
}
