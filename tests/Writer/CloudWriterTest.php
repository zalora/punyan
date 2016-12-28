<?php
namespace Zalora\Punyan\Writer;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use Zalora\Punyan\TimeProvider;

/**
 * @package Zalora\Punyan\Writer
 * @author Steve Hoang <tuanhoang.minh@zalora.com>
 */
class CloudWriterTest extends \PHPUnit_Framework_TestCase
{

    /** @var CloudWriter we are trying to unit test now */
    private $cloud;

    /** @var CloudWatchLogsClient | \PHPUnit_Framework_MockObject_MockObject */
    private $client;

    /** @var  TimeProvider | \PHPUnit_Framework_MockObject_MockObject */
    private $timeProvider;

    /**
     * @var string
     */
    private $config;

    protected function setUp()
    {

        $this->client = $this->getMockBuilder(CloudWatchLogsClient::class)
            ->disableOriginalConstructor()->getMock();
        $this->timeProvider = $this->getMockBuilder(TimeProvider::class)->disableOriginalConstructor()->getMock();

        $this->config = [
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
                'key' => 'AKIAJTPT2YQSICV7Q7BA',
                'secret' => '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',
            ],
            'logGroup' => '/bob',
            'logStream' => 'bob.json'
        ];
        $this->cloud = new CloudWriter($this->config);

        $this->cloud->setClient($this->client);
        $this->cloud->setTimeProvider($this->timeProvider);

        $this->cloud->setConfig($this->config);
        $this->cloud->setLogGroup($this->config['logGroup']);
        $this->cloud->setLogStream($this->config['logStream']);
    }

    public function testInitLogGroupAndLogStreamCorrectlySetup()
    {
        // Arrange & Given
        $this->client->expects($this->once())->method('createLogGroup')
            ->with([
                'logGroupName' => '/bob'
            ]);
        // Assert & Then
        $this->client->expects($this->once())->method(('createLogStream'))
            ->with([
                'logGroupName'  => '/bob',
                'logStreamName' => 'bob.json'
            ]);
        // Action & when
        $this->cloud->setUp();
    }

    public function testWriteInformationCorrectlyWrittenToCloudWatch()
    {
        // Arrange & Given
        $newTime = round(microtime(true) * 1000);
        $this->timeProvider->method('getMicroTime')->willReturn($newTime);



        $this->client->expects($this->once())->method('putLogEvents')
            ->with([
                'logGroupName' => '/bob',
                'logStreamName' => 'bob.json',
                'logEvents' =>[
                    [
                        'message'=>'hi',
                        'timestamp'=> $newTime
                    ]
                ],
                'sequenceToken' => '',
            ]);

        // Action & When
        $this->cloud->writeToCloudWatch("hi");
    }

    public function testWriteSilentlyFailWhenCloudWatchNotWorking()
    {
        // Arrange
        $newTime = microtime(true);
        $this->timeProvider->method('getMicroTime')->willReturn($newTime);
        $this->client->method('putLogEvents')
            ->willThrowException(new CloudWatchLogsException("Fake exception"));

        try {
            $this->cloud->writeToCloudWatch("hi");
        } catch (CloudWatchLogsException $e) {
            $this->fail('An exception should have been thrown.');
        }
    }
}
