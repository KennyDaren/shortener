<?php

namespace Shortener\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170724101327 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sh_link (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, hash VARCHAR(25) DEFAULT NULL, url VARCHAR(255) NOT NULL, status INT UNSIGNED DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_9E08608D1B862B8 (hash), INDEX IDX_9E08608A76ED395 (user_id), INDEX hash_idx (hash), INDEX url_idx (url), INDEX status_idx (status), INDEX hash_status_idx (hash, status), INDEX url_status_idx (url, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sh_stats (id INT UNSIGNED AUTO_INCREMENT NOT NULL, link_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, date_time DATETIME NOT NULL, device SMALLINT UNSIGNED DEFAULT NULL, platform VARCHAR(30) DEFAULT NULL, browser VARCHAR(30) DEFAULT NULL, city VARCHAR(120) DEFAULT NULL, country VARCHAR(120) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, referer VARCHAR(255) DEFAULT NULL, refererBaseUrl VARCHAR(255) DEFAULT NULL, hour SMALLINT UNSIGNED NOT NULL, week_day SMALLINT UNSIGNED NOT NULL, INDEX IDX_9319610DADA40271 (link_id), INDEX IDX_9319610DA76ED395 (user_id), INDEX dateTime_ix (date_time), INDEX dateTime_link_ix (date_time, link_id), INDEX dateTime_link_user_ix (date_time, link_id, user_id), INDEX dateTime_user_ix (date_time, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sh_users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, email_to_change VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', token VARCHAR(32) DEFAULT NULL, status INT UNSIGNED DEFAULT 0 NOT NULL, INDEX users_email_ix (email), INDEX users_status_ix (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sh_link ADD CONSTRAINT FK_9E08608A76ED395 FOREIGN KEY (user_id) REFERENCES sh_users (id)');
        $this->addSql('ALTER TABLE sh_stats ADD CONSTRAINT FK_9319610DADA40271 FOREIGN KEY (link_id) REFERENCES sh_link (id)');
        $this->addSql('ALTER TABLE sh_stats ADD CONSTRAINT FK_9319610DA76ED395 FOREIGN KEY (user_id) REFERENCES sh_users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sh_stats DROP FOREIGN KEY FK_9319610DADA40271');
        $this->addSql('ALTER TABLE sh_link DROP FOREIGN KEY FK_9E08608A76ED395');
        $this->addSql('ALTER TABLE sh_stats DROP FOREIGN KEY FK_9319610DA76ED395');
        $this->addSql('DROP TABLE sh_link');
        $this->addSql('DROP TABLE sh_stats');
        $this->addSql('DROP TABLE sh_users');
    }
}
