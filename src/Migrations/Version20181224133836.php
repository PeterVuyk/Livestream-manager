<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181224133836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('INSERT INTO `app_users` (`username`,`password`,`email`,`is_active`,`roles`) VALUES (\'temporary\',\'$2y$13$HFR9K9Mw0MtWpTxZaFkUn.fbhPbQQTpcE1t3fYWKDHi1nVoqGs1xO\',\'temporary@example.com\',1,\'a:1:{i:0;s:9:\"ROLE_USER\";}\');');
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DELETE FROM `app_users` WHERE `username`=\'temporary\';');
    }
}
