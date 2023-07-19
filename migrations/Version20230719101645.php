<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719101645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_group DROP INDEX UNIQ_7E954D5BEDDA9608, ADD INDEX IDX_7E954D5BEDDA9608 (domain_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_group DROP INDEX IDX_7E954D5BEDDA9608, ADD UNIQUE INDEX UNIQ_7E954D5BEDDA9608 (domain_group_id)');
    }
}
