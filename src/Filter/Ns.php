<?php
/**
 * Check if the namespace + classname of the class either starts with the given string,
 * contains the given string or matches a regular expression (search methods: (startsWith|contains|regexp))
 * 'startsWith' is the default, if class is not set, the message is sorted out
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
    protected $searchMethod;

    /**
     * @var array
     */
    protected $validSearchMethods = [
        self::SEARCH_METHOD_STARTS_WITH,
        self::SEARCH_METHOD_CONTAINS,
        self::SEARCH_METHOD_REGEXP
    ];

    /**
     * @return void
     * @throws \RuntimeException
     */
    public function init()
    {
        $this->expectedNamespace = $this->config['namespace'];

        if (empty($this->config['searchMethod'])) {
            $this->config['searchMethod'] = static::DEFAULT_SEARCH_METHOD;
        }
        $this->searchMethod = $this->config['searchMethod'];

        if (!in_array($this->searchMethod, $this->validSearchMethods)) {
            throw new \RuntimeException(
                sprintf('Search method must be one of those: %s', implode(', ', $this->validSearchMethods))
            );
        }

        // Validate Regexp
        if ($this->searchMethod === static::SEARCH_METHOD_REGEXP) {
            if (@preg_match($this->expectedNamespace, '') === false) {
                throw new \RuntimeException('Invalid regular expression: ' . $this->expectedNamespace);
            }
        }
    }

    /**
     * Those coverage comments uglify the code quite a lot, but otherwise this area is "not executed"
     * @param LogEvent $event
     * @return bool
     */
    public function accept(LogEvent $event) : bool
    {
        if (empty($event['origin']['class'])) {
            return false;
        }

        switch ($this->searchMethod) {
            case static::SEARCH_METHOD_STARTS_WITH:
                return (strpos($event['origin']['class'], $this->expectedNamespace) === 0);
            case static::SEARCH_METHOD_CONTAINS:
                $pos = strpos($event['origin']['class'], $this->expectedNamespace);
                if ($pos === false) {
                    return false;
                }

                return ($pos >= 0);
            case static::SEARCH_METHOD_REGEXP:
                return (preg_match($this->expectedNamespace, $event['origin']['class']) > 0);
        }
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreStop
}
