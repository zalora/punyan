<?php
/**
 * PHP implementation of Bunyan Logger
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 * @link https://github.com/zalora/punyan
 */

namespace Zalora\Punyan;

use SplObjectStorage;
use Zalora\Punyan\Writer\IWriter;
use Zalora\Punyan\Filter\IFilter;
use Zalora\Punyan\Filter\Priority;

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
     * @var SplObjectStorage
     */
    protected $filters;

    /**
     * Initialize with the app name and an options array
     * @param string $appName
     * @param array $options
     */
    public function __construct($appName, array $options) {
        $this->appName = $appName;
        $this->options = $options;
        $this->writers = new SplObjectStorage();
        $this->filters = new SplObjectStorage();

        // Init logger filters
        foreach ($this->options['filters'] as $filter) {
            $filterName = key($filter);
            $className = sprintf('Zalora\Punyan\Filter\%s',
                ucfirst($filterName)
            );
            $this->filters->attach(new $className(current($filter)));
        }

        // Init writers
        foreach ($this->options['writers'] as $writer) {
            $writerName = key($writer);
            $className = sprintf('Zalora\Punyan\Writer\%s',
                ucfirst($writerName)
            );
            $this->writers->attach(new $className(current($writer)));
        }
    }

    /**
     * @param int $priority
     * @param string $msg
     * @param array $context
     * @return void
     */
    public function log($priority, $msg, array $context = array()) {
        // Check for mute and existing writers
        if (count($this->writers) === 0 || $this->options['mute'] === true) {
            return;
        }

        $logEvent = LogEvent::create($msg, $priority, $context);

        // Check global filters
        $accept = true;

        /* @var $filter Filter\IFilter */
        foreach ($this->filters as $filter) {
            if (!($filter->accept($logEvent) && $accept)) {
                return;
            }
        }

        if ($msg instanceof \Exception) {
            echo $msg->getMessage() . PHP_EOL;
        } else {
            echo $msg . PHP_EOL;
        }

        if (!empty($context)) {
            print_r($context);
        }
    }

    /**
     * @param IWriter $writer
     */
    public function addWriter(IWriter $writer) {
        $this->writers->attach($writer->init());
    }

    /**
     * @param IWriter $writer
     */
    public function removeWriter(IWriter $writer) {
        $this->writers->detach($writer);
    }

    /**
     * @param IFilter $filter
     */
    public function addFilter(IFilter $filter) {
        $this->filters->attach($filter);
    }

    /**
     * @param IFilter $filter
     */
    public function removeFilter(IFilter $filter) {
        $this->filters->detach($filter);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function fatal($msg, array $context = array()) {
        $this->log(static::LEVEL_FATAL, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function error($msg, array $context = array()) {
        $this->log(static::LEVEL_ERROR, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function warn($msg, array $context = array()) {
        $this->log(static::LEVEL_WARN, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function info($msg, array $context = array()) {
        $this->log(static::LEVEL_INFO, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function debug($msg, array $context = array()) {
        $this->log(static::LEVEL_DEBUG, $msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     */
    public function trace($msg, array $context = array()) {
        $this->log(static::LEVEL_TRACE, $msg, $context);
    }

}