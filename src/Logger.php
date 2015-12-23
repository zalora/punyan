<?php
/**
 * PHP implementation of Bunyan Logger
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 * @link https://github.com/zalora/punyan
 */

namespace Zalora\Punyan;

use SplObjectStorage;
use Zalora\Punyan\Writer\IWriter;

/**
 * @package Zalora\Punyan
 */
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
     * @var SplObjectStorage
     */
    protected $writers;

    /**
     * Initialize with the app name and an options array
     * @param string $appName
     * @param array $options
     */
    public function __construct($appName, array $options) {
        $this->appName = $appName;
        $this->options = $options;

        $this->writers = new SplObjectStorage();
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
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    public function log($level, $msg, array $context = array()) {
        $this->update($level, $msg, $context);
    }

    /**
     * Add writer instances
     * @param IWriter $writer
     */
    public function addWriter(IWriter $writer) {
        $this->writers->attach($writer->init());
    }

    public function removeWriter(IWriter $writer) {
        $this->writers->detach($writer);
    }

    /**
     * @param string $level
     * @param string|\Exception $msg
     * @param array $context
     */
    protected function update($level, $msg, array $context = array()) {
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
