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
     * @var string
     */
    const WRITER_NAMESPACE = __NAMESPACE__;

    /**
     * Prepare for action
     * @return void
     */
    public function init();

    /**
     * @param LogEvent $logEvent
     * @return bool
     */
    public function log(LogEvent $logEvent);
}
