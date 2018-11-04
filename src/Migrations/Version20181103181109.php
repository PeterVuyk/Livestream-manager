<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181103181109 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ScheduledCommand');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ScheduledCommand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, command VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, arguments VARCHAR(250) DEFAULT NULL COLLATE utf8mb4_unicode_ci, cronExpression VARCHAR(100) DEFAULT NULL COLLATE utf8mb4_unicode_ci, lastExecution DATETIME NOT NULL, lastReturnCode INT DEFAULT NULL, logFile VARCHAR(100) DEFAULT NULL COLLATE utf8mb4_unicode_ci, priority INT NOT NULL, executeImmediately TINYINT(1) NOT NULL, disabled TINYINT(1) NOT NULL, locked TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
