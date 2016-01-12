<?php
/**
 * Test regular expression filter - And yeah I have no idea about Regexp whatsoever...
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class AbstractFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogEvent
     */
    protected $logEvent;

    /**
     * @covers Zalora\Punyan\Filter\AbstractFilter::buildFilters
     */
    public function testEmptyFilterConfig()
    {
        $filters = AbstractFilter::buildFilters(array());

        $this->assertInstanceOf('\\SplObjectStorage', $filters);
        $this->assertCount(0, $filters);

//        $this->assertFalse($nonExistingFieldReturnFalseFilter->accept($this->logEvent));
//        $this->assertTrue($nonExistingFieldReturnTrueFilter->accept($this->logEvent));
    }
}
