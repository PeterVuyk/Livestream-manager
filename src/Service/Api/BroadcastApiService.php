<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Entity\Channel;
use App\Exception\Livestream\CouldNotApiCallBroadcastException;
use App\Repository\ChannelRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BroadcastApiService
{
    /** @var ChannelRepository */
    private $channelRepository;

    /** @var Client */
    private $client;

    /**
     * @param ChannelRepository $channelRepository
     * @param Client $client
     */
    public function __construct(
        ChannelRepository $channelRepository,
        Client $client
    ) {
        $this->channelRepository = $channelRepository;
        $this->client = $client;
    }

    /**
     * @param string $channelName
     * @return string
     * @throws CouldNotApiCallBroadcastException
     */
    public function getStatusLivestream(string $channelName): string
    {
        $channel = $this->getChannel($channelName);
        try {
            $response = $this->client->request(
                'GET',
                rtrim($channel->getHost(), '/') . '/api/v1/livestream/status',
                ['auth' => [$channel->getUserName(), $channel->getSecret()]]
            );
        } catch (GuzzleException $exception) {
            throw CouldNotApiCallBroadcastException::couldNotHandle($channel);
        }

        $responseBody = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() !== 200 || !isset($responseBody['status'])) {
            throw CouldNotApiCallBroadcastException::getStatusStreamFailed($channelName, $response);
        }
        return $responseBody['status'];
    }

    /**
     * @param string $channelName
     * @throws CouldNotApiCallBroadcastException
     */
    public function resetFromFailure(string $channelName): void
    {
        $channel = $this->getChannel($channelName);
        try {
            $response = $this->client->request(
                'PUT',
                rtrim($channel->getHost(), '/') . '/api/v1/livestream/reset',
                ['auth' => [$channel->getUserName(), $channel->getSecret()]]
            );
        } catch (GuzzleException $exception) {
            throw CouldNotApiCallBroadcastException::couldNotHandle($channel);
        }

        if ($response->getStatusCode() !== 201) {
            throw CouldNotApiCallBroadcastException::resetFromFailureFailed($channelName, $response);
        }
    }

    /**
     * @param string $channelName
     * @return Channel
     * @throws CouldNotApiCallBroadcastException
     */
    private function getChannel(string $channelName): Channel
    {
        $channel = $this->channelRepository->findOneBy(['channelName' => $channelName]);
        if (!$channel instanceof Channel) {
            throw CouldNotApiCallBroadcastException::channelNotFound($channelName);
        }
        return $channel;
    }
}
