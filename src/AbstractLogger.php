<?php
/**
 * Log level methods are in the abstract parent class, rest in the child class
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */
namespace Zalora\Punyan;

abstract class AbstractLogger implements ILogger
{
    /**
     * @var array
     */
    private static $levelNameMap = [
        self::LEVEL_TRACE => 'trace',
        self::LEVEL_DEBUG => 'debug',
        self::LEVEL_INFO => 'info',
        self::LEVEL_WARN => 'warn',
        self::LEVEL_ERROR => 'error',
        self::LEVEL_FATAL => 'fatal'
    ];

    /**
     * @param int $level
     * @return string
     */
    public static function getLevelNameByLevel(int $level) : string
    {
        if (!empty(self::$levelNameMap[$level])) {
            return self::$levelNameMap[$level];
        }

        throw new \InvalidArgumentException(sprintf("'%d' is an invalid log level", $level));
    }

    /**
     * @param int $level
     * @return bool
     */
    public static function isValidLogLevel(int $level) : bool
    {
        return !empty(self::$levelNameMap[$level]);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function fatal($msg, array $context = [])
    {
        $this->log(static::LEVEL_FATAL, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function error($msg, array $context = [])
    {
        $this->log(static::LEVEL_ERROR, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function warn($msg, array $context = [])
    {
        $this->log(static::LEVEL_WARN, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function info($msg, array $context = [])
    {
        $this->log(static::LEVEL_INFO, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function debug($msg, array $context = [])
    {
        $this->log(static::LEVEL_DEBUG, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function trace($msg, array $context = [])
    {
        $this->log(static::LEVEL_TRACE, $msg, $context);
    }

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    public abstract function log(int $level, $msg, array $context = []);
}
