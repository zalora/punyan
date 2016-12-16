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
        $this->logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Foo Bar', [], 'PHPUnit');
    }

    /**
     * Invalid regular expressions throw a RuntimeException, they're checked during init()
     * @expectedException \RuntimeException
     */
    public function testInvalidRegexp()
    {
        $config = json_decode(sprintf($this->regexpConfigStub, '^Foo', '', 'false'), true);
        new Regexp($config);
    }

    /**
     * The default field is msg, custom fields can be configured via a parameter; the regular expressions
     * in this test match
     */
    public function testMatchingRegexp()
    {
        $configDefaultField = json_decode(sprintf($this->regexpConfigStub, '/^Foo/', '', 'false'), true);
        $matchingRegexpFilterWithDefaultField = new Regexp($configDefaultField);

        $configCustomField = json_decode(sprintf($this->regexpConfigStub, '/Unit/', 'name', 'false'), true);
        $matchingRegexpFilterWithCustomField = new Regexp($configCustomField);

        $this->assertTrue($matchingRegexpFilterWithDefaultField->accept($this->logEvent));
        $this->assertTrue($matchingRegexpFilterWithCustomField->accept($this->logEvent));
    }

    /**
     * Also test default and custom fields, only that the regular expressions don't match
     */
    public function testNonMatchingRegexp()
    {
        $configDefaultField = json_decode(sprintf($this->regexpConfigStub, '/^Bar/', '', 'false'), true);
        $nonMatchingRegexpFilterWithDefaultField = new Regexp($configDefaultField);

        $configCustomField = json_decode(sprintf($this->regexpConfigStub, '/phpunit/', 'name', 'false'), true);
        $nonMatchingRegexpFilterWithCustomField = new Regexp($configCustomField);

        $this->assertFalse($nonMatchingRegexpFilterWithDefaultField->accept($this->logEvent));
        $this->assertFalse($nonMatchingRegexpFilterWithCustomField->accept($this->logEvent));
    }

    /**
     * If the field to run the regular expression does not exist, you can configure what should be returned
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

    /**
     * The default field is msg, nested fields are named like 'levelone.leveltwo.levelthree'
     */
    public function testMatchingRegexpWithNestedFieldName()
    {
        $configCustomField = json_decode(sprintf($this->regexpConfigStub, '|^/|', 'proc.uri', 'false'), true);
        $context = [
            'proc' => [
                'uri' => '/info.php'
            ]
        ];

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'Foo Bar', $context, 'PHPUnit');
        $regex = new Regexp($configCustomField);

        $this->assertTrue($regex->accept($logEvent));
    }
}
