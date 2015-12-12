<?php
/**
 * @author Wolfram Huesken <woh@m18.io>
 * @link https://github.com/Lunatic666/Punyan
 */

namespace Zalora\Punyan;

class Logger implements ILogger {

    /**
     * @var string
     */
    protected $appName;

    /**
     * @var array
     */
    protected $options;

    /**
     * Initialize with the app name and an options array
     * @param string $appName
     * @param array $options
     */
    public function __construct($appName, array $options) {
        $this->appName = $appName;
        $this->options = $options;
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function fatal($msg, array $context = array()) {
        $this->write(static::LEVEL_FATAL, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function error($msg, array $context = array()) {
        $this->write(static::LEVEL_ERROR, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function warn($msg, array $context = array()) {
        $this->write(static::LEVEL_WARN, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function info($msg, array $context = array()) {
        $this->write(static::LEVEL_INFO, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function debug($msg, array $context = array()) {
        $this->write(static::LEVEL_DEBUG, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function trace($msg, array $context = array()) {
        $this->write(static::LEVEL_TRACE, $msg, $context);
    }

    /**
     * @param string $level
     * @param string|\Exception $msg
     * @param array $context
     */
    protected function write($level, $msg, array $context = array()) {
        if ($msg instanceof \Exception) {
            echo $msg->getMessage() . PHP_EOL;
        } else {
            echo $msg . PHP_EOL;
        }

        if (!empty($context)) {
            print_r($context);
        }
    }

}
