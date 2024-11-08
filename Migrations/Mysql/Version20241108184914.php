<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108184914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE sitegeist_grapevine_domain_model_message (persistence_object_identifier VARCHAR(40) NOT NULL, author VARCHAR(40) DEFAULT NULL, title VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', recipientroles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_31F2B825BDAFD8C8 (author), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sitegeist_grapevine_domain_model_notification (persistence_object_identifier VARCHAR(40) NOT NULL, account VARCHAR(40) DEFAULT NULL, message VARCHAR(40) DEFAULT NULL, INDEX IDX_EA1EFB057D3656A4 (account), INDEX IDX_EA1EFB05B6BD307F (message), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sitegeist_grapevine_domain_model_message ADD CONSTRAINT FK_31F2B825BDAFD8C8 FOREIGN KEY (author) REFERENCES neos_flow_security_account (persistence_object_identifier)');
        $this->addSql('ALTER TABLE sitegeist_grapevine_domain_model_notification ADD CONSTRAINT FK_EA1EFB057D3656A4 FOREIGN KEY (account) REFERENCES neos_flow_security_account (persistence_object_identifier)');
        $this->addSql('ALTER TABLE sitegeist_grapevine_domain_model_notification ADD CONSTRAINT FK_EA1EFB05B6BD307F FOREIGN KEY (message) REFERENCES sitegeist_grapevine_domain_model_message (persistence_object_identifier)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1027Platform'."
        );

        $this->addSql('ALTER TABLE sitegeist_grapevine_domain_model_notification DROP FOREIGN KEY FK_EA1EFB05B6BD307F');
        $this->addSql('DROP TABLE sitegeist_grapevine_domain_model_message');
        $this->addSql('DROP TABLE sitegeist_grapevine_domain_model_notification');
    }
}
