<?php
/**
 * Bean to store additional log data
 * Note that the getters and setters lowercase the first letter of the variable
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

/**
 * @package Zalora\Punyan
 *
 * @method int getPriority() getPriority()
 * @method void setPriority() setPriority(int $priority)
 *
 * @method string getPriorityName() getPriorityName()
 * @method void setPriorityName() setPriorityName(string $priorityName)

 * @method string getMessage() getMessage()
 * @method void setMessage() setMessage(string $message)
 *
 * @method float getTimestamp() getTimestamp()
 * @method void setTimestamp() setTimestamp(float $timestamp)
 */
class LogEvent extends \ArrayObject {

    /**
     * Set basic values
     */
    public function __construct(array $context = array())
    {
        parent::__construct($context);
        if (empty($this->getTimestamp())) {
            $this->setTimestamp(microtime(true));
        }
    }

    /**
     * Create a new log event (Saves a line or two)
     * @param string $msg
     * @param int $priority
     * @param array $context
     * @return LogEvent
     */
    public static function create($msg, $priority, array $context) {
        $logEvent = new LogEvent($context);
        $logEvent->setMessage($msg);
        $logEvent->setPriority($priority);

        return $logEvent;
    }

    /**
     * Default getters and setters
     * @param string $name
     * @param mixed $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (strlen($name) < 4) {
            throw new \RuntimeException('Logevents only support getters and setters');
        }

        $prefix = substr($name, 0, 3);
        if ($prefix !== 'get' && $prefix !== 'set') {
            throw new \RuntimeException('Logevents only support getters and setters');
        }

        $varName = lcfirst(substr($name, 3));

        if ($prefix === 'get') {
            if (!empty($this[$varName])) {
                return $this[$varName];
            } else { return; }
        }

        if ($prefix === 'set') {
            if (empty($arguments)) {
                throw new \RuntimeException('Setters should set a value, right?');
            }
            $this[$varName] = $arguments[0];
        }
    }
}
