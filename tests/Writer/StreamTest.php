<?php
/**
 * I don't want to mess around with virtual filesystems, so I use php://memory as stream
 * Should be pretty much the same as any other stream
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;
use Zalora\Punyan\Formatter\Bunyan;
use Zalora\Punyan\StreamWrapper\VariableStream;

/**
 * @package Zalora\Punyan\Writer
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test a normal logging operation
     */
    public function testLog()
    {
        $config = array(
            'mute' => false,
            'url' => 'php://memory',
            'filters' => array()
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
     * php://memory doesn't support locking, but as flock() is a PHP core method
     * I don't really have to unit test it. I pretty much stole a demo wrapper from the PHP documentation
     * and made lie that it supports locking...
     */
    public function testLogWithLock() {
        stream_wrapper_register("globalz", "Zalora\\Punyan\\StreamWrapper\\VariableStream");

        $config = array(
            'lock' => true,
            'mute' => false,
            'url' => 'globalz://foobar',
            'filters' => array()
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
     * Everyone can open a valid URL, only Chuck Norris can open this
     */
    public function testOpenInvalidUrl()
    {
        $this->setExpectedException('\\RuntimeException');

        $config = array(
            'mute' => false,
            'url' => 'chuckNorris://TryToOpenThis',
            'filters' => array()
        );
        new Stream($config);
    }
}
