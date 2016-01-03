<?php
/**
 * Check if the namespace + classname of the class either starts with the given string
 * or contains the given string (search methods: (startsWith|contains). The former is the default
 * If the call comes from a script without classname, it's filtered out
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
    const SEARCH_METHOD_STARTS_WITH = 'startsWith';

    /**
     * @var string
     */
    const SEARCH_METHOD_CONTAINS = 'contains';

    /**
     * @var string
     */
    const SEARCH_METHOD_REGEXP = 'regexp';

    /**
     * @var string
     */
    const DEFAULT_SEARCH_METHOD = self::SEARCH_METHOD_STARTS_WITH;

    /**
     * @var string
     */
    protected $expectedNamespace;

    /**
     * @var string
     */
    protected $logClassNamespace;

    /**
     * @var string
     */
    protected $searchMethod;

    /**
     * @var array
     */
    protected $validSearchMethods = array(
        self::SEARCH_METHOD_STARTS_WITH,
        self::SEARCH_METHOD_CONTAINS,
        self::SEARCH_METHOD_REGEXP
    );

    /**
     * @return void
     * @throws RuntimeException
     */
    public function init()
    {
        $this->expectedNamespace = $this->config['namespace'];
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $bt = array_pop($bt);

        if (!empty($bt['class'])) {
            $this->logClassNamespace = $bt['class'];
        }

        if (empty($this->config['searchMethod'])) {
            $this->searchMethod = static::DEFAULT_SEARCH_METHOD;
        } else {
            if (!in_array($this->config['searchMethod'], $this->validSearchMethods)) {
                throw new \RuntimeException(
                    sprintf('Search method must be one of those: %s', implode(', ', $this->validSearchMethods))
                );
            }
            $this->searchMethod = $this->config['searchMethod'];
        }

        // Validate Regexp
        if ($this->searchMethod === static::SEARCH_METHOD_REGEXP) {
            if (@preg_match($this->expectedNamespace, '') === false) {
                throw new \RuntimeException('Invalid regular expression: ' . $this->expectedNamespace);
            }
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

        switch ($this->searchMethod) {
            case static::SEARCH_METHOD_STARTS_WITH:
                return (strpos($this->logClassNamespace, $this->expectedNamespace) === 0);
            case static::SEARCH_METHOD_CONTAINS:
                $pos = strpos($this->logClassNamespace, $this->expectedNamespace);
                if ($pos === false) {
                    return false;
                }

                return ($pos >= 0);
            case static::SEARCH_METHOD_REGEXP:
                return (preg_match($this->expectedNamespace, $this->logClassNamespace) > 0);
        }
    }
}
