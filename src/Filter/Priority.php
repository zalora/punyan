<?php
/**
 * Filter by priority
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class Priority extends AbstractFilter
{
    /**
     * @var string
     */
    const DEFAULT_OPERATOR = '>=';

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @return void
     */
    protected function init()
    {
        $this->priority = $this->getPriority();
        if (empty($this->config['operator'])) {
            $this->operator = static::DEFAULT_OPERATOR;
        } else {
            $this->operator = $this->config['operator'];
        }
    }

    /**
     * @param LogEvent $event
     * @return bool
     */
    public function accept(LogEvent $event)
    {
        return version_compare($event['level'], $this->priority, $this->operator);
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function getPriority()
    {
        if (empty($this->config['priority'])) {
            throw new \RuntimeException("Priority filter doesn't have priority");
        }

        $priority = $this->config['priority'];
        if (is_string($priority)) {
            $priority = @constant(sprintf('Zalora\Punyan\ILogger::LEVEL_%s', strtoupper($priority)));
        }

        if (!is_numeric($priority)) {
            throw new \RuntimeException('Priority must be a string-constant defined in ILogger');
        }

        return (int) $priority;
    }
}
