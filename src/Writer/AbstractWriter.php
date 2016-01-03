<?php
/**
 * Convenience class to implement new writers faster
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\Filter\IFilter;
use Zalora\Punyan\LogEvent;
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
        // Check writer filters
        $accept = true;

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
     * @return void
     */
    protected abstract function _write(LogEvent $logEvent);
}
