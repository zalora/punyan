<?php
/**
 * Test Priority Filter
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $priorityConfigDefaultStub = '{"priority": "%s"}';

    /**
     * @var string
     */
    protected $priorityConfigStub = '{"priority": "%s", "operator": "%s"}';

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * Create some log events with different prios
     */
    protected function setUp()
    {
        $this->filters[ILogger::LEVEL_TRACE] = LogEvent::create(ILogger::LEVEL_TRACE, '', array(), 'PHPUnit');
        $this->filters[ILogger::LEVEL_INFO] = LogEvent::create(ILogger::LEVEL_INFO, '', array(), 'PHPUnit');
        $this->filters[ILogger::LEVEL_WARN] = LogEvent::create(ILogger::LEVEL_WARN, '', array(), 'PHPUnit');
        $this->filters[ILogger::LEVEL_ERROR] = LogEvent::create(ILogger::LEVEL_ERROR, '', array(), 'PHPUnit');
        $this->filters[ILogger::LEVEL_FATAL] = LogEvent::create(ILogger::LEVEL_FATAL, '', array(), 'PHPUnit');
    }

    /**
     * @covers Zalora\Punyan\Filter\Priority::accept
     */
    public function testGreaterOrEqualDefaultOperator() {
        $config = json_decode(sprintf($this->priorityConfigDefaultStub, 'warn'), true);
        $priorityFilter = new Priority($config);

        // Greater or equal prios will be accepted
        $this->assertTrue($priorityFilter->accept($this->filters[ILogger::LEVEL_ERROR]));
        $this->assertTrue($priorityFilter->accept($this->filters[ILogger::LEVEL_FATAL]));
        $this->assertTrue($priorityFilter->accept($this->filters[ILogger::LEVEL_WARN]));

        // Lesser prios will not be accepted
        $this->assertFalse($priorityFilter->accept($this->filters[ILogger::LEVEL_INFO]));
        $this->assertFalse($priorityFilter->accept($this->filters[ILogger::LEVEL_TRACE]));
    }

    /**
     * @covers Zalora\Punyan\Filter\Priority::accept
     */
    public function testGreaterOperator() {
        $config = json_decode(sprintf($this->priorityConfigStub, 'warn', '>'), true);
        $priorityFilter = new Priority($config);

        // Greater prios will be accepted
        $this->assertTrue($priorityFilter->accept($this->filters[ILogger::LEVEL_ERROR]));
        $this->assertTrue($priorityFilter->accept($this->filters[ILogger::LEVEL_FATAL]));

        // Lesser or equal prios will not be accepted
        $this->assertFalse($priorityFilter->accept($this->filters[ILogger::LEVEL_INFO]));
        $this->assertFalse($priorityFilter->accept($this->filters[ILogger::LEVEL_TRACE]));
        $this->assertFalse($priorityFilter->accept($this->filters[ILogger::LEVEL_WARN]));
    }

    /**
     * @covers Zalora\Punyan\Filter\Priority::accept
     */
    public function testFatalFilterWithLesserOperator() {
        $config = json_decode(sprintf($this->priorityConfigStub, 'fatal', '>'), true);
        $priorityFatal = new Priority($config);

        // Fatal with greater operator is like muting
        $this->assertFalse($priorityFatal->accept($this->filters[ILogger::LEVEL_FATAL]));
        $this->assertFalse($priorityFatal->accept($this->filters[ILogger::LEVEL_ERROR]));
    }
}
