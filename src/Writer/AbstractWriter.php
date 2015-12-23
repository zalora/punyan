<?php
/**
 * Convenience class to implement new writers faster
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

/**
 * @package Zalora\Punyan\Writer
 */
abstract class AbstractWriter implements IWriter {

    /**
     * @var array
     */
    protected $config;

    /**
     * @var int
     */
    protected $logLevel;

    /**
     * Store configuration
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->logLevel = $this->getLogLevel();
    }

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    public function log($level, $msg, array $context = array())
    {
        $this->_write($level, $msg, $context);
    }

    protected function getCalledClass() {
        $class = get_called_class();
        if (strpos($class, '\\') > 0) {
            return substr(strrchr(get_called_class(), '\\'), 1);
        }

        return $class;
    }

    protected function getLogLevel() {
        $logLevelConstant = sprintf('LEVEL_%s',
            strtoupper($this->config[$this->getCalledClass()]['logLevel'])
        );

        return static::$logLevelConstant;
    }


    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    protected abstract function _write($level, $msg, array $context = array());

}
