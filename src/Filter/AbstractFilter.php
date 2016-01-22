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
     * @var string
     */
    const FILTER_NAMESPACE_STUB = 'Zalora\Punyan\Filter\%s';

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
     * @param array $config
     * @return \SplObjectStorage
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function buildFilters(array $config)
    {
        $filters = new \SplObjectStorage();

        foreach ($config as $filter) {
            if (empty($filter) || !is_array($filter)) {
                throw new \InvalidArgumentException('Invalid configuration');
            }

            $filterName = key($filter);
            if (!class_exists($filterName)) {
                $className = sprintf(static::FILTER_NAMESPACE_STUB,
                    ucfirst($filterName)
                );

                if (!class_exists($className)) {
                    throw new \RuntimeException(sprintf("Class '%s' not found...", $filterName));
                }

                $filter = new $className(current($filter));
            } else {
                $filter = new $filterName(current($filter));
            }

            if (!($filter instanceof IFilter)) {
                throw new \RuntimeException(sprintf("Filter '%s' does not implement IFilter", $filterName));
            }
            $filters->attach($filter);
        }

        return $filters;
    }
}
