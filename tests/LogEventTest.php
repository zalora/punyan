<?php
/**
 * Test the LogEvent
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

/**
 * @package Zalora\Punyan
 */
class LogEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RuntimeException
     */
    protected $runtimeEx;

    /**
     * @var \InvalidArgumentException
     */
    protected $invargEx;

    /**
     * Setup two exceptions
     */
    public function setUp()
    {
        $this->runtimeEx = new \RuntimeException('Run in time', 123);
        $this->invargEx = new \InvalidArgumentException('Invalid Argument', 456, $this->runtimeEx);
    }

    /**
     * Log events have to be created with an integer value log level, otherwise
     * a RuntimeException is thrown
     * @see \Zalora\Punyan\ILogger
     * @expectedException \TypeError
     */
    public function testCreateWithInvalidLogLevel()
    {
        LogEvent::create('Good morning', 'Hallo Test', [], 'PHPUnit');
    }

    /**
     * App name is mandatory, if a log event is created without an InvalidArgumentException is thrown
     * @expectedException \InvalidArgumentException
     */
    public function testCreateWithEmptyAppname()
    {
        LogEvent::create(ILogger::LEVEL_WARN, 'Hallo Test', [], '');
    }

    /**
     * Log events accept a predefined timestamp with microseconds
     */
    public function testCreatePredefinedTime()
    {
        $time = sprintf('%d.1234', time());
        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo Test', ['time' => $time], 'PHPUnit');

        $this->assertEquals('1234', substr($logEvent->getTime(), -5, -1));
    }

    /**
     * If a predefined timestamp does not contain microseconds, it's filled up with zeros
     */
    public function testCreatePredefinedTimeWithoutMicroseconds()
    {
        $time = time();
        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo Test', ['time' => $time], 'PHPUnit');

        $this->assertEquals('0000', substr($logEvent->getTime(), -5, -1));
    }

    /**
     * Log events also take an exception as message argument (here without previous exception)
     */
    public function testCreateWithExceptionWithoutPrevious()
    {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, $this->runtimeEx, [], 'PHPUnit');
        $logEventData = $logEvent->getArrayCopy();

        $this->assertNotEmpty($logEventData);
        $this->assertInternalType('array', $logEventData);

        $this->assertArrayHasKey('msg', $logEventData);
        $this->assertArrayHasKey('exception', $logEventData);

        $this->assertEquals($this->runtimeEx->getMessage(), $logEventData['msg']);
        $this->assertNotEmpty($logEventData['exception']);
        $this->assertInternalType('array', $logEventData['exception']);

        $exceptionArray = $logEventData['exception'];
        $this->assertArrayHasKey('file', $exceptionArray);
        $this->assertArrayHasKey('message', $exceptionArray);
        $this->assertArrayHasKey('code', $exceptionArray);
        $this->assertArrayHasKey('line', $exceptionArray);
        $this->assertArrayHasKey('trace', $exceptionArray);
        $this->assertArrayNotHasKey('previous', $exceptionArray);

        $this->assertEquals(__FILE__, $exceptionArray['file']);
        $this->assertEquals($this->runtimeEx->getMessage(), $exceptionArray['message']);
        $this->assertEquals($this->runtimeEx->getCode(), $exceptionArray['code']);
        $this->assertEquals($this->runtimeEx->getLine(), $exceptionArray['line']);
        $this->assertNotEmpty($exceptionArray['trace']);
    }

    /**
     * Log events also take an exception as message argument (here with previous exception)
     */
    public function testCreateWithExceptionWithPrevious()
    {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, $this->invargEx, [], 'PHPUnit');
        $logEventData = $logEvent->getArrayCopy();

        $this->assertNotEmpty($logEventData);
        $this->assertInternalType('array', $logEventData);

        $this->assertArrayHasKey('msg', $logEventData);
        $this->assertArrayHasKey('exception', $logEventData);

        $this->assertEquals($this->invargEx->getMessage(), $logEventData['msg']);
        $this->assertNotEmpty($logEventData['exception']);
        $this->assertInternalType('array', $logEventData['exception']);

        $exceptionArray = $logEventData['exception'];
        $this->assertArrayHasKey('file', $exceptionArray);
        $this->assertArrayHasKey('message', $exceptionArray);
        $this->assertArrayHasKey('code', $exceptionArray);
        $this->assertArrayHasKey('line', $exceptionArray);
        $this->assertArrayHasKey('trace', $exceptionArray);
        $this->assertArrayHasKey('previous', $exceptionArray);

        $this->assertEquals(__FILE__, $exceptionArray['file']);
        $this->assertEquals($this->invargEx->getMessage(), $exceptionArray['message']);
        $this->assertEquals($this->invargEx->getCode(), $exceptionArray['code']);
        $this->assertEquals($this->invargEx->getLine(), $exceptionArray['line']);
        $this->assertNotEmpty($exceptionArray['trace']);

        $previousArray = $exceptionArray['previous'];
        $this->assertArrayHasKey('file', $previousArray);
        $this->assertArrayHasKey('message', $previousArray);
        $this->assertArrayHasKey('code', $previousArray);
        $this->assertArrayHasKey('line', $previousArray);
        $this->assertArrayHasKey('trace', $previousArray);
        $this->assertArrayNotHasKey('previous', $previousArray);

        $this->assertEquals(__FILE__, $previousArray['file']);
        $this->assertEquals($this->runtimeEx->getMessage(), $previousArray['message']);
        $this->assertEquals($this->runtimeEx->getCode(), $previousArray['code']);
        $this->assertEquals($this->runtimeEx->getLine(), $previousArray['line']);
        $this->assertNotEmpty($previousArray['trace']);
    }

    /**
     * Log events only support getters and setters, if you try to call other methods,
     * a BadFunctionCallException is thrown. Methods shorter than 4 chars are invalid,
     * because they can't be a valid getter or setter
     * @expectedException \BadFunctionCallException
     */
    public function testShortInvalidMethodCall()
    {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Hallo', [], 'PHPUnit');
        $logEvent->get();
    }

    /**
     * Methods longer than 3 chars have to start with 'get' or 'set' to be valid,
     * otherwise a BadFunctionCallException is thrown
     * @expectedException \BadFunctionCallException
     */
    public function testLongInvalidMethodCall()
    {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Hallo', [], 'PHPUnit');
        $logEvent->fooBar();
    }

    /**
     * Getters and setters are implemented with the __call() function, setters of course
     * have to set a value, if called without it throws an InvalidArgumentException
     * @expectedException \InvalidArgumentException
     */
    public function testEmptySetter()
    {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Hallo', [], 'PHPUnit');
        $logEvent->setTime();
    }

    /**
     * If you try to get a variable which does not exist, you get back null
     */
    public function testEmptyGetter()
    {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Hallo', [], 'PHPUnit');

        $this->assertNull($logEvent->getADcUX3qKyFnITCF());
        $this->assertNull($logEvent->getCLkJM6kFz8JvYEj());
        $this->assertNull($logEvent->getWyGe3Hmx1PuNCkk());
        $this->assertNull($logEvent->getETTnCLoCS6WQR3A());
        $this->assertNull($logEvent->getDdTTNTWUniTh1ci());
        $this->assertNull($logEvent->getWb044rUn13Z7Ry3());
        $this->assertNull($logEvent->getQpVFftHPFtjAG3z());
        $this->assertNull($logEvent->getA2ViCINc7Q4hBoP());
        $this->assertNull($logEvent->getNw5tCJdwGdBj0jb());
        $this->assertNull($logEvent->getIbH32Vin29OIDiX());
    }

    /**
     * Arrange an exception with a bean with a broken toArray() function
     * Not that realistic, but has to be covered nevertheless
     */
    public function testArgumentInExceptionTraceThrowsException()
    {
        try {
            fopen(new BrokenBean, 'r');
        } catch (\PHPUnit_Framework_Error_Warning $ex) {
            $logEvent = LogEvent::create(ILogger::LEVEL_FATAL, $ex, [], 'PHPUnit');
            // Iterate over the trace and check that they all have a type and a value
            foreach ($logEvent['exception']['trace'] as $trace) {
                foreach ($trace['args'] as $arg) {
                    $this->assertArrayHasKey('type', $arg);
                    $this->assertArrayHasKey('value', $arg);
                }
            }
        }
    }

    /**
     * Test if exception handler is actually executed
     * Setting the exception handler from outside is tested in LoggerTest::testExternalExceptionHandler()
     * @see LoggerTest::testExternalExceptionHandler()
     */
    public function testExternalExceptionHandler()
    {
        $exHandler = function(\Throwable $ex) {
            $e = [];
            $e['message'] = $ex->getMessage();
            $e['exceptionHandler'] = __METHOD__;

            return $e;
        };

        $logEvent = LogEvent::create(ILogger::LEVEL_FATAL, new \Exception('Hallo'), [], 'PHPUnit', $exHandler);

        $this->assertArrayHasKey('message', $logEvent['exception']);
        $this->assertArrayHasKey('exceptionHandler', $logEvent['exception']);

        $this->assertEquals($logEvent['exception']['message'], 'Hallo');
        $this->assertStringEndsWith('{closure}', $logEvent['exception']['exceptionHandler']);
    }

    /**
     * Test if the log level name is dragged along properly
     */
    public function testLogLevelName()
    {
        $event = LogEvent::create(ILogger::LEVEL_TRACE, 'Hallo', [], 'PHPUnit');
        $this->assertEquals($event->getLevelName(), Logger::getLevelNameByLevel($event->getLevel()));

        $event->setLevel(ILogger::LEVEL_DEBUG);
        $this->assertEquals($event->getLevelName(), Logger::getLevelNameByLevel($event->getLevel()));

        $event->setLevel(ILogger::LEVEL_INFO);
        $this->assertEquals($event->getLevelName(), Logger::getLevelNameByLevel($event->getLevel()));

        $event->setLevel(ILogger::LEVEL_WARN);
        $this->assertEquals($event->getLevelName(), Logger::getLevelNameByLevel($event->getLevel()));

        $event->setLevel(ILogger::LEVEL_ERROR);
        $this->assertEquals($event->getLevelName(), Logger::getLevelNameByLevel($event->getLevel()));

        $event->setLevel(ILogger::LEVEL_FATAL);
        $this->assertEquals($event->getLevelName(), Logger::getLevelNameByLevel($event->getLevel()));
    }
}

/**
 * Needed to cause an exception during conversion
 * @package Zalora\Punyan
 */
class BrokenBean
{
    /**
     * @var int
     */
    public $age = 33;

    /**
     * @throws \RuntimeException
     */
    public function toArray() {
        throw new \RuntimeException("I'm a broken bean");
    }
}
