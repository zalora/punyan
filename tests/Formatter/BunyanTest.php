<?php
/**
 * Test the formatter
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Formatter;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class BunyanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zalora\Punyan\Formatter\Bunyan::format
     */
    public function testFormat() {
        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo Test', array(), 'PHPUnit');
        $formatter = new Bunyan();
        $formattedString = $formatter->format($logEvent);

        // Must be well formed JSON
        $this->assertJson($formattedString);

        // Decode it and test if the fields the formatter added are present
        // The other fields will be tested in the LogEvent test
        $logEventArray = json_decode($formattedString, true);

        $this->assertNotEmpty($logEventArray);
        $this->assertInternalType('array', $logEventArray);

        $this->assertArrayHasKey('hostname', $logEventArray);
        $this->assertArrayHasKey('pid', $logEventArray);
        $this->assertArrayHasKey('v', $logEventArray);
    }
}
