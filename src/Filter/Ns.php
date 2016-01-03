<?php
/**
 * Check if the namespace of the class starts with the given string
 * If the logging class doesn't have a namespace it's filtered out
 * Namespace is a keyword in PHP, hence the stupid classname
 *
 * I'm using debug_backtrace(), it's surprisingly fast, so I don't
 * see any problems in using it, although it feels a bit hacky
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class Ns extends AbstractFilter
{
    /**
     * @var string
     */
    protected $expectedNamespace;

    /**
     * @var string
     */
    protected $logClassNamespace;

    /**
     * @return void
     */
    public function init()
    {
        $this->expectedNamespace = $this->config['namespace'];
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $bt = array_pop($bt);

        if (!empty($bt['class'])) {
            $this->logClassNamespace = $bt['class'];
        }
    }

    /**
     * @param LogEvent $event
     * @return bool
     */
    public function accept(LogEvent $event)
    {
        if (empty($this->logClassNamespace)) {
            return false;
        }

        return (strpos($this->logClassNamespace, $this->expectedNamespace) === 0);
    }
}
