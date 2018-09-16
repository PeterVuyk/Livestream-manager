<?php
declare(strict_types=1);

namespace App\Tests\App\Controller\Api;

use App\Controller\Api\LivestreamController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class LivestreamControllerTest extends TestCase
{
    /** @var LivestreamController */
    private $livestreamController;

    public function setUp()
    {
        $this->livestreamController = new LivestreamController();
    }

    public function testStartLivestream()
    {
        $response = $this->livestreamController->startLivestream();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
