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
}
