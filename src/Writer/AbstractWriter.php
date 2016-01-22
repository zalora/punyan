<?php
/**
 * Convenience class to implement new writers faster
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\Logger;
use Zalora\Punyan\LogEvent;
use Zalora\Punyan\Filter\IFilter;
use Zalora\Punyan\Formatter\Bunyan;
use Zalora\Punyan\Formatter\IFormatter;
use Zalora\Punyan\Filter\AbstractFilter;

/**
 * @package Zalora\Punyan\Writer
 */
abstract class AbstractWriter implements IWriter
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \SplObjectStorage
     */
    protected $filters;

    /**
     * @var IFormatter
     */
    protected $formatter;

    /**
     * @var bool
     */
    protected $bubble;

    /**
     * Store configuration
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        if (!array_key_exists('mute', $config)) {
            $this->config['mute'] = false;
        }

        $this->formatter = new Bunyan();
        $this->filters = AbstractFilter::buildFilters($config['filters']);

        if (array_key_exists('bubble', $this->config) && $this->config['bubble'] === false) {
            $this->bubble = false;
        }

        $this->init();
    }

    /**
     * @param LogEvent $logEvent
     * @return bool
     */
    public function log(LogEvent $logEvent)
    {
        // Check for muting
        if ($this->config['mute'] === true) {
            return;
        }

        // Check writer filters
        $accept = true;

        if (!array_key_exists('origin', $this->config) || $this->config['origin'] === true) {
            $this->addOrigin($logEvent);
        }

        /* @var $filter IFilter */
        foreach ($this->filters as $filter) {
            if (!($filter->accept($logEvent) && $accept)) {
                return;
            }
        }

        // Only return false if the writer really wants to stop propagating
        $bubble = $this->_write($logEvent);
        if ($bubble === false) {
            return false;
        }

        return true;
    }

    /**
     * @param LogEvent $logEvent
     */
    protected function addOrigin(LogEvent $logEvent) {
        $origin = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        if (!empty($origin)) {
            $logEvent['origin'] = $origin;
        }
    }

    /**
     * @param LogEvent $logEvent
     * @return bool
     */
    protected abstract function _write(LogEvent $logEvent);
}
