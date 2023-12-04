<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201110122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proxy (
            `id` INT UNSIGNED AUTO_INCREMENT NOT NULL, 
            `task_id` INT UNSIGNED NOT NULL, 
            `ip` VARCHAR(255) NOT NULL, 
            `port` SMALLINT UNSIGNED NOT NULL, 
            `tc` DATETIME NOT NULL, 
            `type` TINYINT UNSIGNED NOT NULL, 
            `status` TINYINT UNSIGNED NOT NULL, 
            `prop` JSON DEFAULT NULL, 
            `locking` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            INDEX IDX_proxy_task_id (`task_id`),
            INDEX IDX_proxy_status_lock (`status`, `locking`),
            PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE task (
            `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
            PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE proxy ADD CONSTRAINT FK_7372C9BEB8E08577 FOREIGN KEY (task_id) REFERENCES task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proxy DROP FOREIGN KEY FK_7372C9BEB8E08577');
        $this->addSql('DROP TABLE proxy');
        $this->addSql('DROP TABLE task');
    }
}
