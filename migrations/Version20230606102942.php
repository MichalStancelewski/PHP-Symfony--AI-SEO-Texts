<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606102942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD card_header LONGTEXT DEFAULT NULL, ADD card_company_name VARCHAR(255) DEFAULT NULL, ADD card_company_phone VARCHAR(255) DEFAULT NULL, ADD card_company_email VARCHAR(255) DEFAULT NULL, ADD card_company_website VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP card_header, DROP card_company_name, DROP card_company_phone, DROP card_company_email, DROP card_company_website');
    }
}
