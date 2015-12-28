<?php
/**
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

/**
 * Define log level methods
 * @package Zalora
 */
interface ILogger {

    /**
     * The service/app is going to stop or become unusable now - An operator should definitely look into this soon
     * @var int
     */
    const LEVEL_FATAL = 60;

    /**
     * Fatal for a particular request, but the service/app continues servicing other requests
     * An operator should look at this soon(ish)
     * @var int
     */
    const LEVEL_ERROR = 50;

    /**
     * A note on something that should probably be looked at by an operator eventually
     * @var int
     */
    const LEVEL_WARN = 40;

    /**
     * Detail on regular operation
     * @var int
     */
    const LEVEL_INFO = 30;

    /**
     * Anything else, i.e. too verbose to be included in "info" level
     * @var int
     */
    const LEVEL_DEBUG = 20;

    /**
     * Logging from external libraries used by your app or very detailed application logging
     * @var int
     */
    const LEVEL_TRACE = 10;

    /**
     * @param int $priority
     * @param string $msg
     * @param array $context
     * @return void
     */
    public function log($priority, $msg, array $context = array());
}
