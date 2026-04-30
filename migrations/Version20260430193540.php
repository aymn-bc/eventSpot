<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260430193540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY `FK_B26681ED936B2FA`');
        $this->addSql('DROP INDEX IDX_B26681ED936B2FA ON evenement');
        $this->addSql('ALTER TABLE evenement DROP organisateur_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY `FK_8D93D6498E2AD382`');
        $this->addSql('DROP INDEX IDX_8D93D6498E2AD382 ON user');
        $this->addSql('ALTER TABLE user ADD inscription_id INT DEFAULT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE inscriptions_id evenement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649FD02F13 ON user (evenement_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6495DAC5993 ON user (inscription_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD organisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT `FK_B26681ED936B2FA` FOREIGN KEY (organisateur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B26681ED936B2FA ON evenement (organisateur_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FD02F13');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495DAC5993');
        $this->addSql('DROP INDEX IDX_8D93D649FD02F13 ON user');
        $this->addSql('DROP INDEX IDX_8D93D6495DAC5993 ON user');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD inscriptions_id INT DEFAULT NULL, DROP evenement_id, DROP inscription_id, CHANGE email email VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT `FK_8D93D6498E2AD382` FOREIGN KEY (inscriptions_id) REFERENCES inscription (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6498E2AD382 ON user (inscriptions_id)');
    }
}
