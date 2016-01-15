<?php
/**
 * I don't want to mess around with virtual filesystems, so I use php://memory as stream
 * Should be pretty much the same as any other stream
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\Formatter\Bunyan;
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
        $writer->log($logEvent);
    }
}
