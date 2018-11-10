<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181110100441 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE recurring_schedule (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(50) NOT NULL, command VARCHAR(50) NOT NULL, execution_day ENUM(\'monday\', \'tuesday\', \'wednesday\', \'thursday\', \'friday\', \'saturday\', \'sunday\') NOT NULL COMMENT \'(DC2Type:enumWeekDays)\', execution_time TIME NOT NULL, last_execution DATETIME DEFAULT NULL, priority INT NOT NULL, run_with_next_execution TINYINT(1) NOT NULL, disabled TINYINT(1) NOT NULL, wrecked TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE stream_schedule');
        $this->addSql('ALTER TABLE schedule_log ADD CONSTRAINT FK_12F62DCFA35860F2 FOREIGN KEY (stream_schedule_id) REFERENCES recurring_schedule (id)');
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE schedule_log DROP FOREIGN KEY FK_12F62DCFA35860F2');
        $this->addSql('CREATE TABLE stream_schedule (id CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, command VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, cron_expression VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, last_execution DATETIME DEFAULT NULL, priority INT NOT NULL, run_with_next_execution TINYINT(1) NOT NULL, disabled TINYINT(1) NOT NULL, wrecked TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE recurring_schedule');
    }
}
