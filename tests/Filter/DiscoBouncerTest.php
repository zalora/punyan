<?php
/**
 * All for the coverage! Or Unit Tests...
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class DiscoBouncerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Always returns false, good for unit tests
     */
    public function testAccept() {
        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, '', array(), 'PHPUnit');
        $noFilter = new DiscoBouncer(array());

        $this->assertFalse($noFilter->accept($logEvent));
    }
}
