<?php
/**
 * Extend this class to implement new filters
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

/**
 * @package Zalora\Punyan\Filter
 */
abstract class AbstractFilter implements IFilter
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->init();
    }

    /**
     * Override this method if you need initialization
     * @return void
     */
    protected function init() {}

    /**
     * @param array $filters
     * @return \SplObjectStorage
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function buildFilters(array $filters)
    {
        $filterStorage = new \SplObjectStorage();

        foreach ($filters as $filterConfig) {
            if (!is_array($filterConfig)) {
                throw new \InvalidArgumentException('Invalid configuration');
            }

            $filterClass = key($filterConfig);
            if (!class_exists($filterClass)) {
                $filterClass = sprintf('%s\\%s', static::FILTER_NAMESPACE, ucfirst($filterClass));
                if (!class_exists($filterClass)) {
                    throw new \RuntimeException(sprintf("Class '%s' not found...", key($filterConfig)));
                }
            }
            $filter = new $filterClass(current($filterConfig));

            if (!($filter instanceof IFilter)) {
                throw new \RuntimeException(sprintf("Filter '%s' does not implement IFilter", $filterClass));
            }

            $filterStorage->attach($filter);
        }

        return $filterStorage;
    }
}
