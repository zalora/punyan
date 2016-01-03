<?php
/**
 * Use preg_match to check if the pattern matches a given subject
 * To configure the field use variables from context separated by dots
 * Examples:
 *
 * - 'msg' for the log message
 * - request.src for the array structure $logEvent['request']['src']
 *
 * If the field doesn't exist, the filter returns false by default
 * This behaviour can be configured with the parameter 'returnValueOnMissingField'
 * The value in 'returnValueOnMissingField' is also used for incompatible types (arrays, objects)
 * The default field is msg
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class Regexp extends AbstractFilter
{
    /**
     * @var bool
     */
    const DEFAULT_RETURN_VALUE_ON_MISSING_FIELD = false;

    /**
     * @var string
     */
    const DEFAULT_FIELD = 'msg';

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var bool
     */
    protected $returnValueOnMissingField;

    /**
     * @return void
     */
    public function init()
    {
        $this->pattern = $this->config['pattern'];

        if (empty($this->config['field'])) {
            $this->field = static::DEFAULT_FIELD;
        } else {
            $this->field = $this->config['field'];
        }

        if (empty($this->config['returnValueOnMissingField'])) {
            $this->returnValueOnMissingField = static::DEFAULT_RETURN_VALUE_ON_MISSING_FIELD;
        } else {
            $this->returnValueOnMissingField = $this->config['returnValueOnMissingField'];
        }
    }

    /**
     * @param LogEvent $event
     * @return bool
     */
    public function accept(LogEvent $event)
    {
        $value = $this->getFieldValue($event);
        if (is_null($value)) {
            return $this->returnValueOnMissingField;
        }

        return (bool) preg_match($this->pattern, $value);
    }

    /**
     * @param LogEvent $event
     * @return string
     */
    protected function getFieldValue(LogEvent $event) {
        $fields = explode('.', $this->field);
        $value = $event->getArrayCopy();

        for ($x = 0; $x < count($fields); $x++) {
            if (array_key_exists($fields[$x], $value)) {
                $value = $value[$fields[$x]];
            } else {
                break;
            }
        }

        if ($x < count($fields) || is_array($value) || is_object($value)) {
            return null;
        }

        return (string) $value;
    }
}
