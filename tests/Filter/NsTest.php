<?php
/**
 * Test namespace filter methods (startsWith|contains|regexp)
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class NsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $nsConfigStub = '{ "namespace": "%s", "searchMethod": "%s" }';

    /**
     * @covers Zalora\Punyan\Filter\Ns::accept
     */
    public function testStartsWith() {
        $matchingConfig = json_decode(sprintf($this->nsConfigStub, 'Zalora\\\\Punyan', Ns::SEARCH_METHOD_STARTS_WITH), true);
        $notMatchingConfig = json_decode(sprintf($this->nsConfigStub, 'Free_Beer', Ns::SEARCH_METHOD_STARTS_WITH), true);

        $nsMatchingFilter = new Ns($matchingConfig);
        $nsNotMatchingFilter = new Ns($notMatchingConfig);

        $logEventMatching = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');
        $logEventMatching['origin'] = array(
            'class' => __CLASS__
        );

        $logEventNotMatching = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');
        $logEventNotMatching['origin'] = array(
            'class' => __CLASS__
        );

        $this->assertTrue($nsMatchingFilter->accept($logEventMatching));
        $this->assertFalse($nsNotMatchingFilter->accept($logEventNotMatching));
    }

    /**
     * @covers Zalora\Punyan\Filter\Ns::accept
     */
    public function testContains() {
        $matchingConfig = json_decode(sprintf($this->nsConfigStub, 'Test', Ns::SEARCH_METHOD_CONTAINS), true);
        $notMatchingConfig = json_decode(sprintf($this->nsConfigStub, 'Foo', Ns::SEARCH_METHOD_CONTAINS), true);

        $nsMatchingFilter = new Ns($matchingConfig);
        $nsNotMatchingFilter = new Ns($notMatchingConfig);

        $logEventMatching = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');
        $logEventMatching['origin'] = array(
            'class' => __CLASS__
        );

        $logEventNotMatching = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');
        $logEventNotMatching['origin'] = array(
            'class' => __CLASS__
        );

        $this->assertTrue($nsMatchingFilter->accept($logEventMatching));
        $this->assertFalse($nsNotMatchingFilter->accept($logEventNotMatching));
    }

    /**
     * @covers Zalora\Punyan\Filter\Ns::accept
     */
    public function testRegexp() {
        $matchingConfig = json_decode(sprintf($this->nsConfigStub, '/Test/', Ns::SEARCH_METHOD_REGEXP), true);
        $notMatchingConfig = json_decode(sprintf($this->nsConfigStub, '/^Foo/', Ns::SEARCH_METHOD_REGEXP), true);

        $nsMatchingFilter = new Ns($matchingConfig);
        $nsNotMatchingFilter = new Ns($notMatchingConfig);

        $logEventMatching = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');
        $logEventMatching['origin'] = array(
            'class' => __CLASS__
        );

        $logEventNotMatching = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');
        $logEventNotMatching['origin'] = array(
            'class' => __CLASS__
        );

        $this->assertTrue($nsMatchingFilter->accept($logEventMatching));
        $this->assertFalse($nsNotMatchingFilter->accept($logEventNotMatching));
    }

    /**
     * @covers Zalora\Punyan\Filter\Ns::accept
     */
    public function testInvalidRegexp() {
        $this->setExpectedException('\\RuntimeException');

        $config = json_decode(sprintf($this->nsConfigStub, '^Foo', Ns::SEARCH_METHOD_REGEXP), true);
        new Ns($config);
    }
}
