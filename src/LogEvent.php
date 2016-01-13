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
 * @method int getLevel() getLevel()
 *
 * @method string getMsg() getMsg()
 * @method void setMsg() setMsg(string $msg)
 *
 * @method string getTime() getTime()
 * @method void setTime() setTime(string $time)
 *
 * @method array getException() getException()
 * @method void setException() setException(array $exception)
 *
 * @method string getName() getName()
 *
 * @method string getHostname() getHostname()
 * @method void setHostname() setHostname(string $hostname)
 *
 * @method int getPid() getPid()
 * @method void setPid() setPid(int $pid)
 *
 * @method int getV() getV()
 * @method void setV() setV(int $v)
 */
class LogEvent extends \ArrayObject implements ILogger
{
    /**
     * Create a new log event (Saves a line or two)
     * @param int $level
     * @param string|\Exception $msg
     * @param array $context
     * @param string $appName
     * @return LogEvent
     * @throws \InvalidArgumentException
     */
    public static function create($level, $msg, array $context, $appName)
    {
        $logEvent = new LogEvent($context);
        $logEvent->setLevel($level);

        if ($msg instanceof \Exception) {
            $logEvent->setMsg($msg->getMessage());
            $logEvent->setException(
                static::exceptionToArray($msg)
            );
        } else {
            $logEvent->setMsg($msg);
        }

        $logEvent->setName($appName);

        // Set formatted UTC timestamp (Seriously PHP?)
        $timeParts = explode('.', microtime(true));

        // If you're lucky and PHP returns the exact second...
        if (count($timeParts) === 1) {
            $timeParts[1] = '0000';
        }

        // Add some padding if needed
        $timeParts[1] = str_pad($timeParts[1], 4, '0');

        $dt = new \DateTime();
        $dt->setTimezone(new \DateTimeZone('UTC'));
        $dt->setTimestamp($timeParts[0]);
        $utcTime = $dt->format('Y-m-d\TH:i:s.') . $timeParts[1] . 'Z';

        $logEvent->setTime($utcTime);

        return $logEvent;
    }

    /**
     * @param \Exception $ex
     * @return array
     */
    protected static function exceptionToArray(\Exception $ex)
    {
        $e = array();
        $e['file'] = $ex->getFile();
        $e['message'] = $ex->getMessage();
        $e['code'] = $ex->getCode();
        $e['line'] = $ex->getLine();
        $e['trace'] = $ex->getTrace();

        if ($ex = $ex->getPrevious()) {
            $e['previous'] = static::exceptionToArray($ex);
        }

        return $e;
    }

    /**
     * Default getters and setters
     * @param string $name
     * @param mixed $arguments
     * @return mixed|void
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
            } else { return null; }
        }

        if ($prefix === 'set') {
            if (empty($arguments)) {
                throw new \RuntimeException('Setters should set a value, right?');
            }
            $this[$varName] = $arguments[0];
        }

        return null;
    }

    /**
     * @param int $level
     * @throws \InvalidArgumentException
     */
    public function setLevel($level) {
        if ($level <= 0) {
            throw new \InvalidArgumentException('Level must be a positive integer, @see ILogger');
        }

        $this['level'] = (int) $level;
    }

    /**
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public function setName($name) {
        if (empty($name)) {
            throw new \InvalidArgumentException('App name is mandatory');
        }

        $this['name'] = $name;
    }
}
