<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181106185413 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE schedule_log (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', stream_schedule_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', last_run_successful TINYINT(1) DEFAULT NULL, message LONGTEXT NOT NULL, time_executed DATETIME NOT NULL, INDEX IDX_12F62DCFA35860F2 (stream_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stream_schedule DROP last_run_successful');
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE schedule_log');
        $this->addSql('ALTER TABLE stream_schedule ADD last_run_successful TINYINT(1) DEFAULT NULL');
    }
}
