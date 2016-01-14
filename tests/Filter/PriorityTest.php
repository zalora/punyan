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
     * Create some log events with different priorities
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
     * In normal environments an operator shouldn't be defined, this setting is assumed as
     * default for most users
     */
    public function testGreaterOrEqualDefaultOperator()
    {
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
     * Zend has it, so we support it, too :D
     */
    public function testGreaterOperator()
    {
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
     * In this edge case nothing is locked (That's why you usually don't mess around with the operator)
     */
    public function testFatalFilterWithLesserOperator()
    {
        $config = json_decode(sprintf($this->priorityConfigStub, 'fatal', '>'), true);
        $priorityFatal = new Priority($config);

        // Fatal with greater operator is like muting
        $this->assertFalse($priorityFatal->accept($this->filters[ILogger::LEVEL_FATAL]));
        $this->assertFalse($priorityFatal->accept($this->filters[ILogger::LEVEL_ERROR]));
    }

    /**
     * Initializing a prio filter without prio gives you a RuntimeException
     */
    public function testConfigWithMissingPriority()
    {
        $this->setExpectedException('\\RuntimeException');
        new Priority(array());
    }

    /**
     * Usually you would configure the priority with a string, e.g. 'warn', but using the number is ok as well
     */
    public function testConfigWithNumericPriority()
    {
        new Priority(array('priority' => 10));
    }

    /**
     * Priorities <= 0 are invalid (of course) and trigger an InvalidArgumentException
     */
    public function testConfigWithInvalidNumericPriority()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        new Priority(array('priority' => -12));
    }

    /**
     * Unknown string priorities trigger an InvalidArgumentException as well
     */
    public function testConfigWithInvalidStringPriority()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        new Priority(array('priority' => 'turmoil'));
    }
}
