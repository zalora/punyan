<?php
/**
 * Test no operation processor
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Processor
 */
class NoOpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run the process method and watch it do nothing
     */
    public function testProcess()
    {
        $configWithoutProcessor = [
            'url' => 'php://memory',
            'filters' => []
        ];

        $configWithProcessor = [
            'url' => 'php://memory',
            'filters' => [],
            'processors' => [['NoOp' => []]]
        ];

        $logEvent = LogEvent::create(ILogger::LEVEL_INFO, 'Hello PHPUnit', ['time' => time()], 'PHPUnit');

        $writerWithoutProcessor = new Stream($configWithoutProcessor);
        $writerWithProcessor = new Stream($configWithProcessor);

        $writerWithoutProcessor->log($logEvent);
        $writerWithProcessor->log($logEvent);

        $output = [];
        $output[] = stream_get_contents($writerWithoutProcessor->getStream(), -1, 0);
        $output[] = stream_get_contents($writerWithProcessor->getStream(), -1, 0);

        // Prove that NoOp doesn't do shit
        $this->assertEquals($output[0], $output[1]);
    }
}
