<?php
/**
 * Pretty silly to test this filter, but let's do it for the coverage...
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class NoFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Always return true, good for unit tests
     */
    public function testAccept() {
        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, '', array(), 'PHPUnit');
        $noFilter = new NoFilter(array());

        $this->assertTrue($noFilter->accept($logEvent));
    }
}
