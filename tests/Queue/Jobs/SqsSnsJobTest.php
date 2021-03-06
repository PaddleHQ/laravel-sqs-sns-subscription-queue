<?php

namespace Joblocal\LaravelSqsSnsSubscriptionQueue\Tests\Queue\Jobs;

use PHPUnit\Framework\TestCase;
use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;

use Joblocal\LaravelSqsSnsSubscriptionQueue\Queue\Jobs\SqsSnsJob;

class SqsSnsJobTest extends TestCase
{
    private $sqsClient;
    private $container;

    protected function setUp():void
    {
        $this->sqsClient = $this->getMockBuilder(SqsClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new Container;
    }

    private function createSqsSnsJob($routes = [])
    {
        $body = [
            'TopicArn' => 'TopicArn:123456',
            'Subject' => 'Subject#action',
            'Message' => 'The Message',
        ];
        $payload = [
            'Body' => json_encode($body),
        ];

        return new SqsSnsJob(
            $this->container,
            $this->sqsClient,
            'default_queue',
            $payload,
            $routes
        );
    }

    private function getSqsSnsJobSubjectRoute()
    {
        return $this->createSqsSnsJob([
            'Subject#action' => '\\stdClass',
        ]);
    }

    private function getSqsSnsJobTopicRoute()
    {
        return $this->createSqsSnsJob([
            'TopicArn:123456' => '\\stdClass',
        ]);
    }


    public function testWillResolveSqsSubscriptionJob()
    {
        $jobPayload = $this->getSqsSnsJobSubjectRoute()->payload();

        $this->assertEquals('Illuminate\\Queue\\CallQueuedHandler@call', $jobPayload['job']);
    }

    public function testWillResolveSqsSubscriptionCommandName()
    {
        $jobPayload = $this->getSqsSnsJobSubjectRoute()->payload();

        $this->assertEquals('\\stdClass', $jobPayload['data']['commandName']);
    }

    public function testWillResolveSqsSubscriptionCommand()
    {
        $jobPayload = $this->getSqsSnsJobSubjectRoute()->payload();
        $expectedCommand = serialize(new \stdClass);

        $this->assertEquals($expectedCommand, $jobPayload['data']['command']);
    }


    public function testWillResolveSqsSubscriptionJobTopicRoute()
    {
        $jobPayload = $this->getSqsSnsJobTopicRoute()->payload();

        $this->assertEquals('Illuminate\\Queue\\CallQueuedHandler@call', $jobPayload['job']);
    }

    public function testWillResolveSqsSubscriptionCommandNameTopicRoute()
    {
        $jobPayload = $this->getSqsSnsJobTopicRoute()->payload();

        $this->assertEquals('\\stdClass', $jobPayload['data']['commandName']);
    }

    public function testWillResolveSqsSubscriptionCommandTopicRoute()
    {
        $jobPayload = $this->getSqsSnsJobTopicRoute()->payload();
        $expectedCommand = serialize(new \stdClass);

        $this->assertEquals($expectedCommand, $jobPayload['data']['command']);
    }


    public function testWillLeaveDefaultSqsJobUntouched()
    {
        $body = [
            'Message' => 'The Message',
        ];

        $defaultSqsJob = new SqsSnsJob(
            $this->container,
            $this->sqsClient,
            'default_queue',
            [
                'Body' => json_encode($body),
            ],
            []
        );

        $jobPayload = $defaultSqsJob->payload();

        $this->assertEquals($body, $jobPayload);
    }
}
