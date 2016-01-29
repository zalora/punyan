<?php
/**
 * Parent processor handles configuration
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Processor;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Processor
 */
abstract class AbstractProcessor implements IProcessor
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    protected $onDemand = false;

    /**
     * AbstractProcessor constructor.
     * @param array $config
     */
    public function __construct(array $config = array()) {
        $this->config = $config;

        if (array_key_exists('onDemand', $config) && $config['onDemand'] === true) {
            $this->onDemand = true;
        }

        $this->init();
    }

    /**
     * Override it in case you need to do some preparation stuffs
     */
    public function init()
    {
    }

    /**
     * With on demand true and non existing key, return true to stop execution
     * @param LogEvent $logEvent
     * @return bool
     */
    protected function isOnDemandBailOut(LogEvent $logEvent) {
        if ($this->onDemand === true &&
            (!(array_key_exists(static::PROCESSOR_DATA_KEY, $logEvent) &&
                array_key_exists(static::PROCESSOR_KEY, $logEvent[static::PROCESSOR_DATA_KEY])))) {
            return true;
        }

        return false;
    }
}
