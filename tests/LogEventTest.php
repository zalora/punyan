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
     * @covers Zalora\Punyan\LogEvent::create
     */
    public function testCreateWithInvalidLogLevel() {
        $this->setExpectedException('\\InvalidArgumentException');
        LogEvent::create('Good morning', 'Hallo Test', array(), 'PHPUnit');
    }

    /**
     * @covers Zalora\Punyan\LogEvent::create
     */
    public function testCreateWithEmptyAppname() {
        $this->setExpectedException('\\InvalidArgumentException');
        LogEvent::create(ILogger::LEVEL_WARN, 'Hallo Test', array(), '');
    }

    /**
     * @covers Zalora\Punyan\LogEvent::create
     */
    public function testCreateWithoutExceptionWithoutPrevious() {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, $this->runtimeEx, array(), 'PHPUnit');
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
     * @covers Zalora\Punyan\LogEvent::create
     */
    public function testCreateWithExceptionWithPrevious() {
        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, $this->invargEx, array(), 'PHPUnit');
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
}
