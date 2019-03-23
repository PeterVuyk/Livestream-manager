<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190323082151 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE channel (channel_name VARCHAR(100) NOT NULL, user_name VARCHAR(100) NOT NULL, host VARCHAR(255) NOT NULL, secret VARCHAR(64) NOT NULL, UNIQUE INDEX channel_name (channel_name), PRIMARY KEY(channel_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE camera');
        $this->addSql('DROP TABLE camera_configuration');
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE camera (camera VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, state VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX camera (camera), PRIMARY KEY(camera)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE camera_configuration (`key` VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, value LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX `key` (`key`), PRIMARY KEY(`key`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE channel');
    }
}
