<?php
/**
 * Test Web Processor
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;
use Zalora\Punyan\Processor\IProcessor;
use Zalora\Punyan\Processor\Web;

/**
 * @package Zalora\Punyan\Processor
 */
class WebTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The processor should filter out all variables, because they were added to the original $_SERVER array
     */
    public function testProcessWithFakeVariables()
    {
        $config = array(
            'url' => 'php://memory',
            'filters' => array(),
            'processors' => array(array('Web' => array()))
        );

        /*
         * As there are not many web related variables in $_SERVER when running on CLI, we fake them...
         */
        $server['REQUEST_URI'] = '/test.php';
        $server['REQUEST_METHOD'] = 'HUSTLE';
        $server['SERVER_NAME'] = gethostname();
        $server['HTTP_REFERER'] = 'https://duckduckgo.com/?q=zalora+singapore';

        $_SERVER = array_merge($_SERVER, $server);

        $logEvent = LogEvent::create(ILogger::LEVEL_INFO, 'Hello PHPUnit', array('time' => time()), 'PHPUnit');

        $writer = new Stream($config);
        $writer->log($logEvent);

        $output = stream_get_contents($writer->getStream(), -1, 0);
        $this->assertJson($output);

        $outArr = json_decode($output, true);

        $this->assertArrayHasKey(IProcessor::PROCESSOR_DATA_KEY, $outArr);
        $this->assertEmpty($outArr[IProcessor::PROCESSOR_DATA_KEY]);
    }

    /**
     * Test normal operation with an injected $_SERVER array
     */
    public function testProcess()
    {
        $server['REQUEST_URI'] = '/test.php';
        $server['REQUEST_METHOD'] = 'HUSTLE';
        $server['SERVER_NAME'] = gethostname();
        $server['HTTP_REFERER'] = 'https://duckduckgo.com/?q=zalora+singapore';

        $logEvent = LogEvent::create(ILogger::LEVEL_INFO, 'Hello PHPUnit', array('time' => time()), 'PHPUnit');
        $processor = new Web();
        $processor->process($logEvent, $server);

        $this->assertEquals(
            $server['REQUEST_URI'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['url']
        );

        $this->assertEquals(
            $server['REQUEST_METHOD'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['http_method']
        );

        $this->assertEquals(
            $server['SERVER_NAME'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['server']
        );

        $this->assertEquals(
            $server['HTTP_REFERER'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['referrer']
        );

        $this->assertArrayNotHasKey(
            'ip',
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]
        );
    }

    /**
     * Test onDemand flag
     */
    public function testProcessOnDemand()
    {
        $server['REQUEST_URI'] = '/test.php';
        $server['REQUEST_METHOD'] = 'HUSTLE';
        $server['SERVER_NAME'] = gethostname();
        $server['HTTP_REFERER'] = 'https://duckduckgo.com/?q=zalora+singapore';

        $logEvent = LogEvent::create(ILogger::LEVEL_INFO, 'Hello PHPUnit', array('time' => time()), 'PHPUnit');
        $processorOnDemandTrue = new Web(array(
            'onDemand' => true
        ));

        $processorOnDemandFalse = new Web(array(
            'onDemand' => false
        ));

        $processorOnDemandTrue->process($logEvent, $server);

        $this->assertArrayNotHasKey(IProcessor::PROCESSOR_DATA_KEY, $logEvent);

        $processorOnDemandFalse->process($logEvent, $server);

        $this->assertEquals(
            $server['REQUEST_URI'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['url']
        );

        $this->assertEquals(
            $server['REQUEST_METHOD'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['http_method']
        );

        $this->assertEquals(
            $server['SERVER_NAME'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['server']
        );

        $this->assertEquals(
            $server['HTTP_REFERER'],
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]['referrer']
        );

        $this->assertArrayNotHasKey(
            'ip',
            $logEvent[IProcessor::PROCESSOR_DATA_KEY][Web::PROCESSOR_KEY]
        );
    }
}
