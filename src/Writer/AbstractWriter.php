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
use Zalora\Punyan\Processor\IProcessor;
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
     * @var \SplObjectStorage
     */
    protected $processors;

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

        if (empty($config['filters'])) {
            $config['filters'] = array();
        }
        $this->filters = AbstractFilter::buildFilters($config['filters']);

        // If there are no processors, buildProcessors will return an empty SplObjectStorage
        if (empty($this->config['processors']) || !is_array($this->config['processors'])) {
            $this->config['processors'] = array();
        }
        $this->processors = $this->buildProcessors($this->config['processors']);

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
            return null;
        }

        // Check writer filters
        $accept = true;

        if (!array_key_exists('origin', $this->config) || $this->config['origin'] === true) {
            $this->addOrigin($logEvent);
        }

        /* @var $filter IFilter */
        foreach ($this->filters as $filter) {
            if (!($filter->accept($logEvent) && $accept)) {
                return null;
            }
        }

        /* @var $processor IProcessor */
        foreach ($this->processors as $processor) {
            $processor->process($logEvent);
        }

        // Only return false if the writer really wants to stop propagating
        $bubble = $this->_write($logEvent);
        if ($bubble === false) {
            return false;
        }

        return true;
    }

    /**
     * @param IProcessor $processor
     */
    public function addProcessor(IProcessor $processor)
    {
        $this->processors->attach($processor);
    }

    /**
     * @param IProcessor $processor
     */
    public function removeProcessor(IProcessor $processor)
    {
        $this->processors->detach($processor);
    }

    /**
     * @return \SplObjectStorage
     */
    public function getProcessors()
    {
        return clone $this->processors;
    }

    /**
     * @param IFilter $filter
     */
    public function addFilter(IFilter $filter)
    {
        $this->filters->attach($filter);
    }

    /**
     * @param IFilter $filter
     */
    public function removeFilter(IFilter $filter)
    {
        $this->filters->detach($filter);
    }

    /**
     * @return \SplObjectStorage
     */
    public function getFilters()
    {
        return clone $this->filters;
    }

    /**
     * @param LogEvent $logEvent
     */
    protected function addOrigin(LogEvent $logEvent)
    {
        $origin = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        if (!empty($origin)) {
            $logEvent['origin'] = $origin;
        }
    }

    /**
     * @param array $processors
     * @return \SplObjectStorage
     * @throws \RuntimeException
     */
    protected function buildProcessors(array $processors)
    {
        $processorStorage = new \SplObjectStorage();

        foreach ($processors as $processorName) {
            $processorClass = $processorName;

            if (!class_exists($processorClass)) {
                $processorClass = sprintf('%s\\%s', IProcessor::PROCESSOR_NAMESPACE, $processorName);
                if (!class_exists($processorClass)) {
                    throw new \RuntimeException(sprintf("Class '%s' not found...", $processorName));
                }
            }

            $processor = new $processorClass();
            if (!($processor instanceof IProcessor)) {
                throw new \RuntimeException(sprintf("Processor '%s' does not implement IProcessor", $processorClass));
            }

            $processorStorage->attach($processor);
        }

        return $processorStorage;
    }

    /**
     * @param LogEvent $logEvent
     * @return bool
     */
    protected abstract function _write(LogEvent $logEvent);
}
