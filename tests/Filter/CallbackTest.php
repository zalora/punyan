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
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    public static $noCallback = 'Try to call me!';

    /**
     * @var string
     */
    protected $callbackConfigStub = '{ "function": "%s" }';

    /**
     * Test working callbacks with all possible outcomes
     */
    public function testWorkingCallback()
    {
        $configTrue = json_decode(
            sprintf(
                $this->callbackConfigStub,
                str_replace('\\', '\\\\', __CLASS__) . '::callbackReturningTrue'
            ),
            true
        );

        $configFalse = json_decode(
            sprintf(
                $this->callbackConfigStub,
                str_replace('\\', '\\\\', __CLASS__) . '::callbackReturningFalse'
            ),
            true
        );

        $configNoParam = json_decode(
            sprintf(
                $this->callbackConfigStub,
                str_replace('\\', '\\\\', __CLASS__) . '::callbackWithoutParameterReturningTrue'
            ),
            true
        );

        $configNoParamNoReturn = json_decode(
            sprintf(
                $this->callbackConfigStub,
                str_replace('\\', '\\\\', __CLASS__) . '::callbackWithoutParameterReturningVoid'
            ),
            true
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, '', array(), 'PHPUnit');

        $callbackFilterTrue = new Callback($configTrue);
        $callbackFilterFalse = new Callback($configFalse);
        $callbackFilterNoParam = new Callback($configNoParam);
        $callbackFilterNoParamNoReturn = new Callback($configNoParamNoReturn);

        $this->assertTrue($callbackFilterTrue->accept($logEvent));
        $this->assertFalse($callbackFilterFalse->accept($logEvent));
        $this->assertTrue($callbackFilterNoParam->accept($logEvent));
        $this->assertFalse($callbackFilterNoParamNoReturn->accept($logEvent));
    }

    /**
     * Invalid callbacks lead to a RuntimeException
     */
    public function testInvalidCallback()
    {
        $this->setExpectedException('\\RuntimeException');

        $configNotCallable = json_decode(
            sprintf(
                $this->callbackConfigStub,
                str_replace('\\', '\\\\', __CLASS__) . '::$noCallback'
            ),
            true
        );

        new Callback($configNotCallable);
    }

    /**
     * @param LogEvent $logEvent
     * @return bool
     */
    public static function callbackReturningTrue(LogEvent $logEvent)
    {
        return true;
    }

    /**
     * @param LogEvent $logEvent
     * @return bool
     */
    public static function callbackReturningFalse(LogEvent $logEvent)
    {
        return false;
    }

    /**
     * @return bool
     */
    public static function callbackWithoutParameterReturningTrue()
    {
        return true;
    }

    /**
     * @return void
     */
    public static function callbackWithoutParameterReturningVoid()
    {
    }
}
