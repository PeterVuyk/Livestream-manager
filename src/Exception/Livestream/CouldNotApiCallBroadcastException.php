<?php
declare(strict_types=1);

namespace App\Exception\Livestream;

use App\Entity\Channel;
use Psr\Http\Message\ResponseInterface;

class CouldNotApiCallBroadcastException extends \Exception
{
    const CHANNEL_NOT_FOUND_MESSAGE = 'Failed to get channel from channel: %s, could not setup client.';
    const GET_STATUS_STREAM_MESSAGE = 'Api call to get status livestream failed for channel: %s, Body: %s, Code: %s';
    const RESET_FROM_FAILURE_MESSAGE = 'Api call to reset from failure failed, channel: %s, Body: %s, Code: %s';
    const COULD_NOT_HANDLE_MESSAGE = 'Could not handle rest API call, channel name: %s, host: %s';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function getStatusStreamFailed(string $channel, ResponseInterface $response)
    {
        $message = sprintf(
            self::GET_STATUS_STREAM_MESSAGE,
            $channel,
            $response->getBody()->getContents(),
            $response->getStatusCode()
        );
        return new self($message);
    }

    public static function resetFromFailureFailed(string $channel, ResponseInterface $response)
    {
        $message = sprintf(
            self::RESET_FROM_FAILURE_MESSAGE,
            $channel,
            $response->getBody()->getContents(),
            $response->getStatusCode()
        );
        return new self($message);
    }

    public static function channelNotFound(string $channel)
    {
        $message = sprintf(self::CHANNEL_NOT_FOUND_MESSAGE, $channel);
        return new self($message);
    }

    public static function couldNotHandle(Channel $channel)
    {
        $message = sprintf(self::COULD_NOT_HANDLE_MESSAGE, $channel->getChannelName(), $channel->getHost());
        return new self($message);
    }
}
