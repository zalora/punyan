<?php
/**
 * @author Wolfram Huesken <woh@m18.io>
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
     * @param string|\Exception $msg
     * @param array $context
     */
    public function fatal($msg, array $context = array());

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function error($msg, array $context = array());

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function warn($msg, array $context = array());

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function info($msg, array $context = array());

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function debug($msg, array $context = array());

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function trace($msg, array $context = array());

}