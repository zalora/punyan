<?php
/**
 * Dummy Filter lets everything pass
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class NoFilter extends AbstractFilter
{
    /**
     * @param LogEvent $event
     * @return bool
     */
    public function accept(LogEvent $event) : bool
    {
        return true;
    }
}
