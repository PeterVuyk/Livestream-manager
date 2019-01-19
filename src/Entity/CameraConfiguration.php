<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="camera_configuration", uniqueConstraints={@ORM\UniqueConstraint(name="key", columns={"key"})})
 * @ORM\Entity(repositoryClass="App\Repository\CameraConfigurationRepository")
 */
class CameraConfiguration
{
    const KEY_MIXER_INTERVAL_TIME = 'mixerIntervalTime';
    const KEY_MIXER_RETRY_ATTEMPTS = 'mixerRetryAttempts';
    const KEY_CHECK_IF_MIXER_IS_RUNNING = 'checkIfMixerIsRunning';
    const KEY_MIXER_IP_ADDRESS = 'mixerIPAddress';
    const KEY_LIVESTREAM_SERVER = 'livestreamServer';
    const KEY_INTERVAL_IS_SERVER_AVAILABLE = 'intervalIsServerAvailable';
    const KEY_RETRY_IS_SERVER_AVAILABLE = 'retryIsServerAvailable';
    const KEY_VIDEO_BITRATE = 'videoBitrate';
    const KEY_AUDIO_VOLUME = 'audioVolume';
    const KEY_OUTPUT_VIDEO_LOCATION = 'outputVideoLocation';
    const KEY_HARDWARE_VIDEO_DEVICE = 'hardwareVideoDevice';
    const KEY_CAMERA_LOCATION_APPLICATION = 'cameraLocationApplication';
    const KEY_OUTPUT_STREAM_FORMAT = 'outputStreamFormat';
    const KEY_MAP_AUDIO_CHANNEL = 'mapAudioChannel';
    const KEY_AUDIO_BITRATE = 'audioBitrate';
    const KEY_AUDIO_SAMPLING_FREQUENCY = 'audioSamplingFrequency';
    const KEY_INCREASE_VOLUME_INPUT = 'increaseVolumeInput';
    const KEY_INPUT_CAMERA_ADDRESS = 'inputCameraAddress';
    const KEY_FFMPEG_LOCATION_APPLICATION = 'ffmpegLocationApplication';
    const KEY_STOP_STREAM_COMMAND = 'stopStreamCommand';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="`key`", type="string", nullable=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    private $value;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
