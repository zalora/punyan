<?php
/**
 * Convenience class to implement new writers faster
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

/**
 * @package Zalora\Punyan\Writer
 */
abstract class AbstractWriter implements IWriter {

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * Store configuration
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->init();
    }

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    public function log($level, $msg, array $context = array())
    {
        $this->_write($level, $msg, $context);
    }

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    protected abstract function _write($level, $msg, array $context = array());

}
