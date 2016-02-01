<?php
/**
 * Static wrapper for logger instances
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

/**
 * Acts as a proxy to the real logger
 * @package Zalora\Punyan
 */
class ZLog
{
    /**
     * @var Logger
     */
    private static $instance;

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public static function fatal($msg, array $context = array())
    {
        if (empty(static::$instance)) {
            throw new \RuntimeException('Logger instance not added to proxy yet');
        }

        static::$instance->fatal($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public static function error($msg, array $context = array())
    {
        if (empty(static::$instance)) {
            throw new \RuntimeException('Logger instance not added to proxy yet');
        }

        static::$instance->error($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public static function warn($msg, array $context = array())
    {
        if (empty(static::$instance)) {
            throw new \RuntimeException('Logger instance not added to proxy yet');
        }

        static::$instance->warn($msg, $context);
    }


    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public static function info($msg, array $context = array())
    {
        if (empty(static::$instance)) {
            throw new \RuntimeException('Logger instance not added to proxy yet');
        }

        static::$instance->info($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public static function debug($msg, array $context = array())
    {
        if (empty(static::$instance)) {
            throw new \RuntimeException('Logger instance not added to proxy yet');
        }

        static::$instance->debug($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public static function trace($msg, array $context = array())
    {
        if (empty(static::$instance)) {
            throw new \RuntimeException('Logger instance not added to proxy yet');
        }

        static::$instance->trace($msg, $context);
    }

    /**
     * @param Logger $instance
     */
    public static function setInstance(Logger $instance)
    {
        static::$instance = $instance;
    }

    /**
     * Set the logger instance to null
     * @return void
     */
    public static function resetInstance()
    {
        static::$instance = null;
    }
}
