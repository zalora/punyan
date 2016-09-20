<?php
namespace Zalora\Punyan\Writer;

use Zalora\Punyan\LogEvent;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use Zalora\Punyan\TimeProvider;

/**
 * CloudWriter: put logs into cloud watch logs
 * Usage: Create new CloudWriter with AWS configuration connection,
 * add to Logger by addWriter, then call the _write method
 * It is asynchronous method to put all the log into a file/logstream in cloudwatch server.
 *
 * @author Steve Hoang <tuanhoang.minh@zalora.com>
 */

class CloudWriter extends AbstractWriter
{
    /**
     * @var CloudWatchLogsClient
     */
    private $client;

    /**
     * @var string
     */
    private $logStream;

    /**
     * @var string
     */
    private $logGroup;

    /**
     * @var TimeProvider
     */
    private $timeProvider;

    /**
     * Cloud constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Initialise all its dependencies
     */
    public function init()
    {
        $this->timeProvider = new TimeProvider();
        $this->initClient();
        $this->setUp();
    }

    /**
     *  Set up logGroup and logStream
     */
    public function setUp()
    {
        if (!isset($this->config['logGroup']) || !isset($this->config['logStream'])) {
            throw new \RuntimeException('Incorrect configuration, either logGroup or logStream is not configured');
        }
        $this->logGroup = $this->config['logGroup'];
        $this->logStream = $this->config['logStream'];
        try {
            $this->client->createLogGroup([
                'logGroupName' => $this->logGroup,
            ]);
        } catch (CloudWatchLogsException $e) {
            // We don't want any exception when createLogGroup is not working
        }
        try {
            $this->client->createLogStream([
                'logGroupName' => $this->logGroup,
                'logStreamName' => $this->logStream,
            ]);
        } catch (CloudWatchLogsException $e) {
            // We don't want any exception when createLogStream is not working
        }
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $logStream
     */
    public function setLogStream($logStream)
    {
        $this->logStream = $logStream;
    }

    /**
     * @param string $logGroup
     */
    public function setLogGroup($logGroup)
    {
        $this->logGroup = $logGroup;
    }

    /**
     * @param TimeProvider $timeProvider
     */
    public function setTimeProvider(TimeProvider $timeProvider)
    {
        $this->timeProvider = $timeProvider;
    }

    /**
     * @param CloudWatchLogsClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param LogEvent $logEvent
     * @return bool
     */
    protected function _write(LogEvent $logEvent)
    {
        $line = $this->formatter->format($logEvent);
        $this->writeToCloudWatch($line);

        return $this->bubble;
    }

    /**
     * @param string $line
     */
    public function writeToCloudWatch($line)
    {
        $microTime = $this->timeProvider->getMicroTime();
        $responseModel = $this->client->describeLogStreams([
            'logGroupName' => $this->logGroup,
            'logStreamNamePrefix' => $this->logStream,
        ]);
        $uploadSequenceToken = $responseModel['logStreams']['0']['uploadSequenceToken'];
        try {
            $this->client->putLogEvents([
                'logGroupName' => $this->logGroup,
                'logStreamName' => $this->logStream,
                'logEvents' => [
                    [
                        'message' => $line,
                        'timestamp' => $microTime,
                    ]
                ],
                'sequenceToken' => $uploadSequenceToken
            ]);
        } catch (CloudWatchLogsException $e) {
            // We don't want any exception when putLogEvents is not working
        }
    }

    private function initClient()
    {
        $this->client = CloudWatchLogsClient::factory($this->config);
    }
}
