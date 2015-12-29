<?php
/**
 * Formatter Interface
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Formatter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Formatter
 */
interface IFormatter
{

    /**
     * @param LogEvent $logEvent
     * @return string
     */
    public function format(LogEvent $logEvent);

}
