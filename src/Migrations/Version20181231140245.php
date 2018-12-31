<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181231140245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('ffmpegLocationApplication', '/usr/local/bin/ffmpeg');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('inputCameraAddress', 'tcp://127.0.0.1:8181?listen');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('increaseVolumeInput', 'volume=24dB');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('audioSamplingFrequency', '44100');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('audioBitrate', '192000');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('mapAudioChannel', '0.1.0');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('outputStreamFormat', 'flv rtmp://live.example.com:1935/given/url');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('cameraLocationApplication', '/home/pi/picam/picam');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('hardwareVideoDevice', 'hw:1,0');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('outputVideoLocation', 'tcp://127.0.0.1:8181');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('audioVolume', '1.0');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('videoBitrate', '1500000');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('retryIsServerAvailable', '5');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('intervalIsServerAvailable', '60');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('livestreamServer', 'live.example.com');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('mixerIPAddress', '127.0.0.1');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('checkIfMixerIsRunning', 'true');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('mixerRetryAttempts', '2');");
        $this->addSql("INSERT INTO `camera_configuration` (`key`,`value`) VALUES ('mixerIntervalTime', '60');");
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'ffmpegLocationApplication';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'inputCameraAddress';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'increaseVolumeInput';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'audioSamplingFrequency';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'audioBitrate';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'mapAudioChannel';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'outputStreamFormat';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'cameraLocationApplication';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'hardwareVideoDevice';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'outputVideoLocation';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'audioVolume';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'videoBitrate';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'retryIsServerAvailable';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'intervalIsServerAvailable';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'livestreamServer';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'mixerIPAddress';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'checkIfMixerIsRunning';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'mixerRetryAttempts';");
        $this->addSql("DELETE FROM `camera_configuration` WHERE `key` = 'mixerIntervalTime';");
    }
}
