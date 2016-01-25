<?php
/**
 * I don't want to mess around with virtual filesystems, so I use php://memory as stream
 * Should be pretty much the same as any other stream
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Writer
 */
class NoWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Writer doesn't do anything
     */
    public function testLog()
    {
        $writer = new NoWriter(array(
            'filters' => array(),
            'mute' => false
        ));

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN,'Hallo', array(), 'PHPUnit');
        $this->assertTrue($writer->log($logEvent));
    }

    /**
     * If bubbling is set to false, _write() must return false, otherwise true
     */
    public function testBubbling()
    {
        $configNoBubbling = array(
            'bubble' => false,
            'url' => 'php://memory',
            'filters' => array()
        );

        $configWithBubbling = array(
            'bubble' => true,
            'url' => 'php://memory',
            'filters' => array()
        );

        $configWithBubblingByDefault = array(
            'url' => 'php://memory',
            'filters' => array()
        );

        $configWithFaultyBubbleSetting = array(
            'bubble' => 'yeeha!',
            'url' => 'php://memory',
            'filters' => array()
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Bubble Bobble', array(), 'PHPUnit');

        $noBubbleWriter = new NoWriter($configNoBubbling);
        $configuredBubbleWriter = new NoWriter($configWithBubbling);
        $defaultBubbleWriter = new NoWriter($configWithBubblingByDefault);
        $wrongConfiguredBubbleWriter = new NoWriter($configWithFaultyBubbleSetting);

        $this->assertFalse($noBubbleWriter->log($logEvent));
        $this->assertTrue($configuredBubbleWriter->log($logEvent));
        $this->assertTrue($defaultBubbleWriter->log($logEvent));
        $this->assertTrue($wrongConfiguredBubbleWriter->log($logEvent));
    }
}
