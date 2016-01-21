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
     * Tests some variants of the startsWith filter
     */
    public function testStartsWith()
    {
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
     * Same for contains
     */
    public function testContains()
    {
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
     * And same for regular expressions
     */
    public function testRegexp()
    {
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
     * If you don't provide a search method, 'startsWith' is set as default
     */
    public function testEmptySearchMethod()
    {
        $config = json_decode(sprintf('{ "namespace": "%s" }', 'Zalora\\\\Punyan'), true);
        $filter = new Ns($config);

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');
        $logEvent['origin'] = array(
            'class' => __CLASS__
        );

        $this->assertTrue($filter->accept($logEvent));
    }

    /**
     * If no origin is provided, the filter returns false, this can e.g. happen if someone sets origin to false
     */
    public function testEmptyOrigin()
    {
        $config = json_decode(sprintf('{ "namespace": "%s" }', 'Zalora\\\\Punyan'), true);
        $filter = new Ns($config);

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Hallo', array(), 'PHPUnit');

        $this->assertFalse($filter->accept($logEvent));
    }

    /**
     * Invalid search methods lead to a RuntimeException
     */
    public function testInvalidSearchMethod()
    {
        $this->setExpectedException('\\RuntimeException');

        $config = json_decode(sprintf($this->nsConfigStub, 'Zalora\\\\Punyan', 'dowser'), true);
        new Ns($config);
    }

    /**
     * All regular expressions are tested during init, invalid regular expressions lead to a RuntimeException
     */
    public function testInvalidRegexp()
    {
        $this->setExpectedException('\\RuntimeException');

        $config = json_decode(sprintf($this->nsConfigStub, '^Foo', Ns::SEARCH_METHOD_REGEXP), true);
        new Ns($config);
    }
}
