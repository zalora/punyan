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
     * @var string
     */
    const DEFAULT_EXCEPTION_HANDLER = 'static::exceptionToArray';

    /**
     * Create a new log event (Saves a line or two)
     * @param int $level
     * @param string|\Exception $msg
     * @param array $context
     * @param string $appName
     * @param mixed $exceptionToArrayHandler
     * @return LogEvent
     */
    public static function create($level, $msg, array $context, $appName, $exceptionToArrayHandler = null)
    {
        $logEvent = new LogEvent($context);
        $logEvent->setLevel($level);

        if ($msg instanceof \Exception) {
            $exceptionHandler = $exceptionToArrayHandler;
            if (empty($exceptionHandler) || !is_callable($exceptionHandler)) {
                $exceptionHandler = static::DEFAULT_EXCEPTION_HANDLER;
            }

            $logEvent->setException(call_user_func($exceptionHandler, $msg));
            $msg = $msg->getMessage();
        }
        $logEvent->setMsg($msg);

        $logEvent->setName($appName);
        $logEvent->setTime(static::getTimestamp($context));

        return $logEvent;
    }

    /**
     * @param \Exception $ex
     * @return array
     */
    public static function exceptionToArray(\Exception $ex)
    {
        $e = array();
        $e['file'] = $ex->getFile();
        $e['message'] = $ex->getMessage();
        $e['code'] = $ex->getCode();
        $e['line'] = $ex->getLine();
        $e['trace'] = array();

        // Build the trace anew to make sure the original exception is not modified
        foreach ($ex->getTrace() as $traceItem) {
            $trace = $traceItem;
            $trace['args'] = array();

            if (empty($traceItem['args'])) {
                continue;
            }

            foreach ($traceItem['args'] as $argItem) {
                $arg = array();
                $arg['type'] = gettype($argItem);

                switch ($arg['type']) {
                    case 'object':
                        $arg['class'] = get_class($argItem);
                        foreach (array('__toString', 'toString', 'toArray') as $method) {
                            $arg['value'] = null;
                            if (method_exists($argItem, $method)) {
                                try {
                                    $arg['value'] = @call_user_func(array($argItem, $method));
                                } catch (\Exception $conversionException) {}
                                break;
                            }
                        }
                        break;
                    case 'boolean':
                        $arg['value'] = $argItem ? 'true' : 'false';
                        break;
                    case 'array':
                    default:
                        $arg['value'] = $argItem;
                }

                $trace['args'][] = $arg;
            }

            $e['trace'][] = $trace;
        }

        // Go through previous exceptions recursively
        if ($prevException = $ex->getPrevious()) {
            $e['previous'] = static::exceptionToArray($prevException);
        }

        return $e;
    }

    /**
     * @param array $context
     * @return string
     */
    protected static function getTimestamp(array $context)
    {
        if (empty($context['time'])) {
            $context['time'] = microtime(true);
        }

        $datetime = \DateTime::createFromFormat('U.u', sprintf('%.4F', $context['time']), new \DateTimeZone('UTC'));
        return $datetime->format('Y-m-d\TH:i:s.') . substr($datetime->format('u'), 0, 4) . 'Z';
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
            throw new \BadFunctionCallException('Logevents only support getters and setters');
        }

        $prefix = substr($name, 0, 3);
        if ($prefix !== 'get' && $prefix !== 'set') {
            throw new \BadFunctionCallException('Logevents only support getters and setters');
        }

        $varName = lcfirst(substr($name, 3));

        if ($prefix === 'get') {
            if (empty($this[$varName])) {
                return null;
            }
            return $this[$varName];
        }

        if ($prefix === 'set') {
            if (empty($arguments)) {
                throw new \InvalidArgumentException('Setters should set a value, right?');
            }
            $this[$varName] = $arguments[0];
        }

        return null;
    }

    /**
     * @param int $level
     * @throws \InvalidArgumentException
     */
    public function setLevel($level)
    {
        if ($level <= 0) {
            throw new \InvalidArgumentException('Level must be a positive integer, @see ILogger');
        }

        $this['level'] = (int) $level;
    }

    /**
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('App name is mandatory');
        }

        $this['name'] = $name;
    }
}
