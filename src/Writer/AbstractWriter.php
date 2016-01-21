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

        $this->init();
    }

    /**
     * @param LogEvent $logEvent
     * @return void
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

        $this->_write($logEvent);
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
     * @return void
     */
    protected abstract function _write(LogEvent $logEvent);
}
