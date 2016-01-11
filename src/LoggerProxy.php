<?php
/**
 * Static wrapper for logger instances
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

/**
 * Acts as a proxy to the real adapters
 * @package Zalora\Punyan
 */
class LoggerProxy
{
    /**
     * @var Logger
     */
    private static $instance;

    /**
     * @param $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public static function warn($msg, array $context)
    {
        if (empty(static::$instance)) {
            throw new \RuntimeException('Logger instance not added to proxy yet');
        }

        static::$instance->warn($msg, $context);
    }

    /**
     * @param Logger $instance
     */
    public static function setInstance(Logger $instance) {
        static::$instance = $instance;
    }
}
