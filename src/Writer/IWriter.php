<?php
/**
 * Writer Interface
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Writer
 */
interface IWriter extends ILogger
{

    /**
     * Prepare for action
     * @return void
     */
    public function init();

    /**
     * @param LogEvent $logEvent
     * @return void
     */
    public function log(LogEvent $logEvent);

}
