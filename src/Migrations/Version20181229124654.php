<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181229124654 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_C250282492FC23A8 (username_canonical), UNIQUE INDEX UNIQ_C2502824A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_C2502824C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('INSERT INTO `app_users` (`id`,`username`,`username_canonical`,`email`,`email_canonical`,`enabled`,`salt`,`password`,`last_login`,`confirmation_token`,`password_requested_at`,`roles`) VALUES (1,\'temporary\',\'temporary\',\'temporary@user.nl\',\'temporary@user.nl\',1,NULL,\'$2y$13$Duhg3ECu2y20IR3jyD7G7eFNB0.Fa75.f.h.pSR/BTBI9tqwHjj7G\',\'2018-12-29 16:23:33\',NULL,NULL,\'a:1:{i:0;s:9:\"ROLE_USER\";}\');');
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('CREATE TABLE `app_users` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `username` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
              `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
              `is_active` tinyint(1) NOT NULL,
              `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT \'(DC2Type:array)\',
              PRIMARY KEY (`id`),
              UNIQUE KEY `UNIQ_C2502824F85E0677` (`username`),
              UNIQUE KEY `UNIQ_C2502824E7927C74` (`email`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
        $this->addSql('INSERT INTO `app_users` (`username`,`password`,`email`,`is_active`,`roles`) VALUES (\'temporary\',\'$2y$13$HFR9K9Mw0MtWpTxZaFkUn.fbhPbQQTpcE1t3fYWKDHi1nVoqGs1xO\',\'temporary@example.com\',1,\'a:1:{i:0;s:9:\"ROLE_USER\";}\');');
    }
}
