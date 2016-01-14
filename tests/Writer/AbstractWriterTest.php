<?php
/**
 * Testing Abstract Writer, i.e. filtering, muting, init, etc.
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;
use Zalora\Punyan\Formatter\Bunyan;

/**
 * @package Zalora\Punyan\Writer
 */
class AbstractWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Muted writers should do nothing
     */
    public function testMutedWriter()
    {
        $config = array(
            'mute' => true,
            'url' => 'php://memory',
            'filters' => array()
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Shut up', array(), 'PHPUnit');

        $writer = new Stream($config);
        $writer->log($logEvent);

        $stream = $writer->getStream();
        fseek($stream, 0);

        $this->assertEmpty(stream_get_contents($stream));
    }

    /**
     * Use a writer with a passing filter
     */
    public function testPassFilters()
    {
        $config = array(
            'mute' => false,
            'url' => 'php://memory',
            'filters' => array(
                array('NoFilter' => array())
            )
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Hello Streams', array(), 'PHPUnit');
        $formatter = new Bunyan();

        $writer = new Stream($config);
        $writer->log($logEvent);

        $stream = $writer->getStream();
        fseek($stream, 0);

        $this->assertSame($formatter->format($logEvent), stream_get_contents($stream));
    }

    /**
     * Using a writer with a blocking filter
     */
    public function testNoPassFilters()
    {
        $config = array(
            'mute' => false,
            'url' => 'php://memory',
            'filters' => array(
                array('DiscoBouncer' => array())
            )
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Shut up', array(), 'PHPUnit');

        $writer = new Stream($config);
        $writer->log($logEvent);

        $stream = $writer->getStream();
        fseek($stream, 0);

        $this->assertEmpty(stream_get_contents($stream));
    }
}
