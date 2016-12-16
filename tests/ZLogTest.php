<?php
/**
 * Test the Logger class
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

use Zalora\Punyan\Formatter\Bunyan;

/**
 * @package Zalora\Punyan
 */
class ZLogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Fetching a not set instance will result in a type error, because we're not (yet) able to return null
     * @expectedException \TypeError
     */
    public function testGetEmptyLogger()
    {
        ZLog::getInstance();
    }

    /**
     * After resetting the instance the TypeError should be back
     * @expectedException \TypeError
     */
    public function testTypeErrorAfterReset() {
        $logger = $this->getMemoryLogger();

        ZLog::setInstance($logger);
        $this->assertEquals($logger, ZLog::getInstance());

        ZLog::resetInstance();
        ZLog::getInstance();
    }

    /**
     * I won't explain that...
     */
    public function testGettersAndSetters() {
        $logger = $this->getMemoryLogger();

        ZLog::setInstance($logger);
        $this->assertEquals($logger, ZLog::getInstance());
    }

    /**
     * Test the trace method
     */
    public function testTrace()
    {
        $logger = $this->getMemoryLogger();
        ZLog::setInstance($logger);

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_TRACE, 'PHPUnit', array('time' => $time), 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        ZLog::trace('PHPUnit', array('time' => $time));

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the trace method with empty logger instance
     * @expectedException \RuntimeException
     */
    public function testEmptyTrace()
    {
        ZLog::resetInstance();
        ZLog::trace('PHPUnit');
    }

    /**
     * Test the debug method
     */
    public function testDebug()
    {
        $logger = $this->getMemoryLogger();
        ZLog::setInstance($logger);

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_DEBUG, 'PHPUnit', array('time' => $time), 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        ZLog::debug('PHPUnit', array('time' => $time));

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the debug method with empty logger instance
     * @expectedException \RuntimeException
     */
    public function testEmptyDebug()
    {
        ZLog::resetInstance();
        ZLog::debug('PHPUnit');
    }

    /**
     * Test the info method
     */
    public function testInfo()
    {
        $logger = $this->getMemoryLogger();
        ZLog::setInstance($logger);

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_INFO, 'PHPUnit', array('time' => $time), 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        ZLog::info('PHPUnit', array('time' => $time));

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the info method with empty logger instance
     * @expectedException \RuntimeException
     */
    public function testEmptyInfo()
    {
        ZLog::resetInstance();
        ZLog::info('PHPUnit');
    }

    /**
     * Test the warn method
     */
    public function testWarn()
    {
        $logger = $this->getMemoryLogger();
        ZLog::setInstance($logger);

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'PHPUnit', array('time' => $time), 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        ZLog::warn('PHPUnit', array('time' => $time));

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the warn method with empty logger instance
     * @expectedException \RuntimeException
     */
    public function testEmptyWarn()
    {
        ZLog::resetInstance();
        ZLog::warn('PHPUnit');
    }

    /**
     * Test the error method
     */
    public function testError()
    {
        $logger = $this->getMemoryLogger();
        ZLog::setInstance($logger);

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'PHPUnit', array('time' => $time), 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        ZLog::error('PHPUnit', array('time' => $time));

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the error method with empty logger instance
     * @expectedException \RuntimeException
     */
    public function testEmptyError()
    {
        ZLog::resetInstance();
        ZLog::error('PHPUnit');
    }

    /**
     * Test the fatal method
     */
    public function testFatal()
    {
        $logger = $this->getMemoryLogger();
        ZLog::setInstance($logger);

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_FATAL, 'PHPUnit', array('time' => $time), 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        ZLog::fatal('PHPUnit', array('time' => $time));

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the fatal method with empty logger instance
     * @expectedException \RuntimeException
     */
    public function testEmptyFatal()
    {
        ZLog::resetInstance();
        ZLog::fatal('PHPUnit');
    }

    /**
     * @return Logger
     */
    private function getMemoryLogger()
    {
        return new Logger('PHPUnit', array(
            'filters' => array(),
            'writers' => array(
                array('Stream' => array(
                    'url' => 'php://memory',
                    'filters' => array()
                ))
            )
        ));
    }

    /**
     * PHP 7 needs to rewind the SPLObjectStorage before you can extract items with current...
     * @param Logger $logger
     * @return resource
     */
    private function getCurrentStreamFromLogger(Logger $logger)
    {
        $writers = $logger->getWriters();
        $writers->rewind();

        return $writers->current()->getStream();
    }
}
