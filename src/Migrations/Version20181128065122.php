<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181128065122 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE stream_schedule ADD stream_duration INT NOT NULL, ADD is_running TINYINT(1) NOT NULL, DROP command');
        $this->addSql('ALTER TABLE schedule_log DROP FOREIGN KEY FK_12F62DCFA35860F2');
        $this->addSql('ALTER TABLE schedule_log ADD CONSTRAINT FK_12F62DCFA35860F2 FOREIGN KEY (stream_schedule_id) REFERENCES stream_schedule (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE schedule_log DROP FOREIGN KEY FK_12F62DCFA35860F2');
        $this->addSql('ALTER TABLE schedule_log ADD CONSTRAINT FK_12F62DCFA35860F2 FOREIGN KEY (stream_schedule_id) REFERENCES stream_schedule (id)');
        $this->addSql('ALTER TABLE stream_schedule ADD command VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, DROP stream_duration, DROP is_running');
    }
}
