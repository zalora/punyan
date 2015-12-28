<?php
/**
 * Writer Interface
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\ILogger;

/**
 * @package Zalora\Punyan\Writer
 */
interface IWriter extends ILogger
{

    /**
     * Prepare for action
     * @return IWriter
     */
    public function init();

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    public function log($level, $msg, array $context = array());

}
