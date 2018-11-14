<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181114071419 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE recurring_schedule');
        $this->addSql('ALTER TABLE stream_schedule ADD execution_day VARCHAR(255) DEFAULT NULL, ADD execution_time TIME DEFAULT NULL, ADD onetime_execution_date DATETIME DEFAULT NULL, DROP cron_expression, DROP last_run_successful');
        $this->addSql('ALTER TABLE schedule_log ADD CONSTRAINT FK_12F62DCFA35860F2 FOREIGN KEY (stream_schedule_id) REFERENCES stream_schedule (id)');
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE recurring_schedule (id CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, command VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, execution_day VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, execution_time TIME NOT NULL, last_execution DATETIME DEFAULT NULL, priority INT NOT NULL, run_with_next_execution TINYINT(1) NOT NULL, disabled TINYINT(1) NOT NULL, wrecked TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule_log DROP FOREIGN KEY FK_12F62DCFA35860F2');
        $this->addSql('ALTER TABLE stream_schedule ADD cron_expression VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, ADD last_run_successful TINYINT(1) DEFAULT NULL, DROP execution_day, DROP execution_time, DROP onetime_execution_date');
    }
}
