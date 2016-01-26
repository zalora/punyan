<?php
/**
 * Log level methods are in the abstract parent class, rest in the child class
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */
namespace Zalora\Punyan;


abstract class AbstractLogger implements ILogger
{

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function fatal($msg, array $context = array())
    {
        $this->log(static::LEVEL_FATAL, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function error($msg, array $context = array())
    {
        $this->log(static::LEVEL_ERROR, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function warn($msg, array $context = array())
    {
        $this->log(static::LEVEL_WARN, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function info($msg, array $context = array())
    {
        $this->log(static::LEVEL_INFO, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function debug($msg, array $context = array())
    {
        $this->log(static::LEVEL_DEBUG, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function trace($msg, array $context = array())
    {
        $this->log(static::LEVEL_TRACE, $msg, $context);
    }

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    public abstract function log($level, $msg, array $context = array());
}