<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230720144845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domain (id INT AUTO_INCREMENT NOT NULL, domain_group_id INT NOT NULL, name VARCHAR(512) NOT NULL, INDEX IDX_A7A91E0BEDDA9608 (domain_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE domain_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(512) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_group (id INT AUTO_INCREMENT NOT NULL, domain_group_id INT DEFAULT NULL, name VARCHAR(512) NOT NULL, INDEX IDX_7E954D5BEDDA9608 (domain_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE domain ADD CONSTRAINT FK_A7A91E0BEDDA9608 FOREIGN KEY (domain_group_id) REFERENCES domain_group (id)');
        $this->addSql('ALTER TABLE project_group ADD CONSTRAINT FK_7E954D5BEDDA9608 FOREIGN KEY (domain_group_id) REFERENCES domain_group (id)');
        $this->addSql('ALTER TABLE project ADD project_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEC31A529C FOREIGN KEY (project_group_id) REFERENCES project_group (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEC31A529C ON project (project_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEC31A529C');
        $this->addSql('ALTER TABLE domain DROP FOREIGN KEY FK_A7A91E0BEDDA9608');
        $this->addSql('ALTER TABLE project_group DROP FOREIGN KEY FK_7E954D5BEDDA9608');
        $this->addSql('DROP TABLE domain');
        $this->addSql('DROP TABLE domain_group');
        $this->addSql('DROP TABLE project_group');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_2FB3D0EEC31A529C ON project');
        $this->addSql('ALTER TABLE project DROP project_group_id');
    }
}
