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
use Zalora\Punyan\Filter\AbstractFilter;

/**
 * @package Zalora\Punyan
 */
class Logger extends AbstractLogger
{
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
    public function __construct($appName, array $options)
    {
        if (empty($appName)) {
            throw new \InvalidArgumentException('Your logger instance must have a name');
        }

        $this->appName = $appName;
        $this->options = $options;
        $this->writers = new SplObjectStorage();

        if (!array_key_exists('filters', $this->options)) {
            $this->options['filters'] = array();
        }

        if (!array_key_exists('writers', $this->options)) {
            $this->options['writers'] = array();
        }

        if (!array_key_exists('mute', $this->options)) {
            $this->options['mute'] = false;
        }

        $this->filters = AbstractFilter::buildFilters($this->options['filters']);
        $this->writers = $this->buildWriters($this->options['writers']);
    }

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    public function log($level, $msg, array $context = array())
    {
        // Check for mute and existing writers
        if (count($this->writers) === 0 || $this->options['mute'] === true) {
            return;
        }

        if (!is_numeric($level) || $level <= 0) {
            throw new \InvalidArgumentException('Invalid log level, please choose one from interface ILogger');
        }

        $logEvent = LogEvent::create($level, $msg, $context, $this->appName);

        // Check global filters
        $accept = true;

        /* @var $filter Filter\IFilter */
        foreach ($this->filters as $filter) {
            if (!($filter->accept($logEvent) && $accept)) {
                return;
            }
        }

        // Send log event to Writers (Threads would be cool here...)
        /* @var $writer IWriter */
        foreach ($this->writers as $writer) {
            $bubble = $writer->log($logEvent);
            if ($bubble === false) {
                break;
            }
        }
    }

    /**
     * @param IWriter $writer
     */
    public function addWriter(IWriter $writer)
    {
        $this->writers->attach($writer);
    }

    /**
     * Return a clone to not accidentally modify stuffs
     * @param bool $getClone
     * @return SplObjectStorage
     */
    public function getWriters($getClone = true)
    {
        if ($getClone) {
            return clone $this->writers;
        }

        return $this->writers;
    }

    /**
     * @param IWriter $writer
     */
    public function removeWriter(IWriter $writer)
    {
        $this->writers->detach($writer);
    }

    /**
     * @param IFilter $filter
     */
    public function addFilter(IFilter $filter)
    {
        $this->filters->attach($filter);
    }

    /**
     * Return a clone to not accidentally modify stuffs
     * @param bool $getClone
     * @return SplObjectStorage
     */
    public function getFilters($getClone = true)
    {
        if ($getClone) {
            return clone $this->filters;
        }

        return $this->filters;
    }

    /**
     * @param IFilter $filter
     */
    public function removeFilter(IFilter $filter)
    {
        $this->filters->detach($filter);
    }

    /**
     * @param array $writers
     * @return SplObjectStorage
     * @throws \RuntimeException
     */
    protected function buildWriters(array $writers)
    {
        $writerStorage = new SplObjectStorage();

        foreach ($writers as $writerConfig) {
            if (empty($writerConfig) || !is_array($writerConfig) || !is_array(current($writerConfig))) {
                throw new \InvalidArgumentException('Invalid writer configuration');
            }

            $writerClass = key($writerConfig);
            if (!class_exists($writerClass)) {
                $writerClass = sprintf('%s\\%s', IWriter::WRITER_NAMESPACE, ucfirst($writerClass));
                if (!class_exists($writerClass)) {
                    throw new \RuntimeException(sprintf("Class '%s' not found...", key($writerConfig)));
                }
            }

            $writer = new $writerClass(current($writerConfig));

            if (!($writer instanceof IWriter)) {
                throw new \RuntimeException(sprintf("Writer '%s' does not implement IWriter", $writerClass));
            }

            $writerStorage->attach($writer);
        }

        return $writerStorage;
    }

    /**
     * @param array $backtrace
     * @return array
     */
    public static function getLogOrigin(array $backtrace) {
        if (count($backtrace) < 2) {
            return array();
        }

        // Safely kick the first item
        array_shift($backtrace);

        $previousItem = null;
        $stackItem = array();

        foreach ($backtrace as $stackItem) {
            if (empty($stackItem['class'])) {
                unset($stackItem['type']);
                return $stackItem;
            }

            if (strpos(ltrim($stackItem['class'], '\\'), __NAMESPACE__) === 0) {
                $previousItem = $stackItem;
                continue;
            }

            unset($stackItem['type']);
            $stackItem['file'] = isset($previousItem['file']) ? $previousItem['file'] : null;
            $stackItem['line'] = isset($previousItem['line']) ? $previousItem['line'] : null;

            break;
        }

        return $stackItem;
    }
}
