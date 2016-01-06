<?php
/**
 * Execute code from the given callback method
 * The LogEvent object is given as parameter, so the callback must be
 * either accept no parameter or the log event parameter with type hinting
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class Callback extends AbstractFilter
{
    /**
     * @var callable
     */
    protected $func;

    /**
     * @return void
     * @throws \RuntimeException
     */
    public function init()
    {
        if (empty($this->config['function']) || !is_callable($this->config['function'])) {
            throw new \RuntimeException('Function is not callable: ' . $this->config['function']);
        }

        $this->func = $this->config['function'];
    }

    /**
     * @param LogEvent $event
     * @return bool
     */
    public function accept(LogEvent $event)
    {
        return call_user_func($this->func, $event);
    }
}
