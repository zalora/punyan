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
class Web implements IProcessor
{
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
     * @param LogEvent $event
     * @return void
     */
    public function process(LogEvent $event)
    {
        foreach ($this->fields as $key => $field) {
            if (empty($_SERVER[$field])) {
                continue;
            }

            $event[static::PROCESSOR_KEY][$key] = $_SERVER[$field];
        }
    }
}
