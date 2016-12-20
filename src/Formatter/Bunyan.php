<?php
/**
 * Transforms a log event into a proper Bunyan compatible string
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Formatter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Formatter
 */
class Bunyan implements IFormatter
{
    /**
     * @var int
     */
    const BUNYAN_VERSION = 0;

    /**
     * @param LogEvent $logEvent
     * @return string
     */
    public function format(LogEvent $logEvent) : string
    {
        $logEvent->setHostname(gethostname());
        $logEvent->setPid(getmypid());
        $logEvent->setV(static::BUNYAN_VERSION);

        return json_encode($logEvent->getArrayCopy()) . PHP_EOL;
    }
}
