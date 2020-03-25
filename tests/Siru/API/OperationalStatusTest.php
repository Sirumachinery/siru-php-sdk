<?php
namespace Siru\Tests\API;

use Siru\API\OperationalStatus;

class OperationalStatusTest extends AbstractApiTest
{

    /**
     * @var OperationalStatus
     */
    private $api;

    public function setUp()
    {
        parent::setUp();
        $this->api = new OperationalStatus($this->signature, $this->transport);
    }

    /**
     * @test
     */
    public function apiStatusIsReported()
    {
        $this->transport
            ->expects($this->exactly(3))
            ->method('request')
            ->with([], '/status', 'GET')
            ->willReturnOnConsecutiveCalls(
                [200, ''],
                [500, ''],
                [503, '']
            );

        $this->assertSame(200, $this->api->check());
        $this->assertSame(500, $this->api->check());
        $this->assertSame(503, $this->api->check());
    }

}
