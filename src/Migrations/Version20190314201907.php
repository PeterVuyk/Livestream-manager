<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190314201907 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DELETE FROM `app_users` WHERE `username`=\'temporary\';');
        $this->addSql('ALTER TABLE app_users ADD channel VARCHAR(255) NOT NULL');
        $this->addSql('INSERT INTO `app_users` (`id`,`username`,`username_canonical`,`email`,`email_canonical`,`enabled`,`salt`,`password`,`last_login`,`confirmation_token`,`password_requested_at`,`roles`,`locale`,`channel`) VALUES (2,\'temporary\',\'temporary\',\'temporary@user.nl\',\'temporary@user.nl\',1,NULL,\'$2y$13$Duhg3ECu2y20IR3jyD7G7eFNB0.Fa75.f.h.pSR/BTBI9tqwHjj7G\',\'2018-12-29 16:23:33\',NULL,NULL,\'a:2:{i:0;s:9:\"ROLE_USER\";i:1;s:16:\"ROLE_SUPER_ADMIN\";}\',\'en\',\'Admin\');');
        $this->addSql('ALTER TABLE stream_schedule ADD channel VARCHAR(100) NOT NULL');
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE app_users DROP channel');
        $this->addSql('ALTER TABLE stream_schedule DROP channel');
    }
}
