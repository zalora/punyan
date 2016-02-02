<?php
/**
 * Add some vars from $_SERVER
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Processor;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Processor
 */
class Web extends AbstractProcessor
{
    /**
     * @var string
     */
    const PROCESSOR_KEY = 'Web';

    /**
     * Fields to take from $_SERVER
     * @var array
     */
    protected $fields = array(
        'url'         => 'REQUEST_URI',
        'ip'          => 'REMOTE_ADDR',
        'http_method' => 'REQUEST_METHOD',
        'server'      => 'SERVER_NAME',
        'referrer'    => 'HTTP_REFERER'
    );

    /**
     * @var array
     */
    protected $filterSetup = array(
        'REQUEST_URI' => FILTER_SANITIZE_STRING,
        'REMOTE_ADDR' => FILTER_VALIDATE_IP,
        'REQUEST_METHOD' => FILTER_SANITIZE_STRING,
        'SERVER_NAME' => array(
            'filter' => FILTER_SANITIZE_STRING,
            'flag' => FILTER_FLAG_HOST_REQUIRED
        ),
        'HTTP_REFERER' => FILTER_VALIDATE_URL
    );

    /**
     * The $_SERVER array must be injectable here, otherwise I can't test it (filter_input_array works too well)
     * @param LogEvent $logEvent
     * @param array $server
     */
    public function process(LogEvent $logEvent, array $server = array())
    {
        if ($this->isOnDemandBailOut($logEvent)) {
            return;
        }

        if (empty($server)) {
            $server = filter_input_array(INPUT_SERVER, $this->filterSetup);
        }

        foreach ($this->fields as $key => $field) {
            if (empty($server[$field])) {
                continue;
            }

            $logEvent[static::PROCESSOR_DATA_KEY][static::PROCESSOR_KEY][$key] = $server[$field];
        }
    }
}
