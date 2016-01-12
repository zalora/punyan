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
class RegexpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $regexpConfigStub = '{ "pattern": "%s", "field": "%s", "returnValueOnMissingField": %s }';

    /**
     * @var LogEvent
     */
    protected $logEvent;

    /**
     * Create log event
     */
    public function setUp()
    {
        $this->logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Foo Bar', array(), 'PHPUnit');
    }

    /**
     * @covers Zalora\Punyan\Filter\Regexp::accept
     */
    public function testInvalidRegexp() {
        $this->setExpectedException('\\RuntimeException');

        $config = json_decode(sprintf($this->regexpConfigStub, '^Foo', '', 'false'), true);
        new Regexp($config);
    }

    /**
     * @covers Zalora\Punyan\Filter\Regexp::accept
     */
    public function testMatchingRegexp() {
        $configDefaultField = json_decode(sprintf($this->regexpConfigStub, '/^Foo/', '', 'false'), true);
        $matchingRegexpFilterWithDefaultField = new Regexp($configDefaultField);

        $configCustomField = json_decode(sprintf($this->regexpConfigStub, '/Unit/', 'name', 'false'), true);
        $matchingRegexpFilterWithCustomField = new Regexp($configCustomField);

        $this->assertTrue($matchingRegexpFilterWithDefaultField->accept($this->logEvent));
        $this->assertTrue($matchingRegexpFilterWithCustomField->accept($this->logEvent));
    }

    /**
     * @covers Zalora\Punyan\Filter\Regexp::accept
     */
    public function testNonMatchingRegexp() {
        $configDefaultField = json_decode(sprintf($this->regexpConfigStub, '/^Bar/', '', 'false'), true);
        $nonMatchingRegexpFilterWithDefaultField = new Regexp($configDefaultField);

        $configCustomField = json_decode(sprintf($this->regexpConfigStub, '/phpunit/', 'name', 'false'), true);
        $nonMatchingRegexpFilterWithCustomField = new Regexp($configCustomField);

        $this->assertFalse($nonMatchingRegexpFilterWithDefaultField->accept($this->logEvent));
        $this->assertFalse($nonMatchingRegexpFilterWithCustomField->accept($this->logEvent));
    }

    /**
     * @covers Zalora\Punyan\Filter\Regexp::accept
     */
    public function testNonExistingFieldFlag()
    {
        $configNonExistingFieldReturnFalse = json_decode(sprintf(
            $this->regexpConfigStub,
            '/Beer/',
            'noHaveLaa',
            'false'
        ), true);

        $configNonExistingFieldReturnTrue = json_decode(sprintf(
            $this->regexpConfigStub,
            '/Beer/',
            'noHaveLaa',
            'true'
        ), true);

        $nonExistingFieldReturnFalseFilter = new Regexp($configNonExistingFieldReturnFalse);
        $nonExistingFieldReturnTrueFilter = new Regexp($configNonExistingFieldReturnTrue);

        $this->assertFalse($nonExistingFieldReturnFalseFilter->accept($this->logEvent));
        $this->assertTrue($nonExistingFieldReturnTrueFilter->accept($this->logEvent));
    }
}
