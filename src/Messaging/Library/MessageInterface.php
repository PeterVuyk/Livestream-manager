<?php
declare(strict_types=1);

namespace App\Messaging\Library;

interface MessageInterface
{
    const RESOURCE_ID = 'resourceId';
    const RESOURCE_ID_KEY = 'resourceIdKey';

    /**
     * @param array $payload
     * @return MessageInterface
     */
    public static function createFromPayload(array $payload): MessageInterface;

    /**
     * @return string
     */
    public function getResourceId(): string;

    /**
     * @return string
     */
    public function getResourceIdKey(): string;

    /**
     * @return array
     */
    public function getPayload(): array;

    /**
     * @return String
     */
    public function messageAction(): String;
}
