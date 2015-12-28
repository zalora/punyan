<?php
/**
 * Interface for all filters
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
interface IFilter
{

    /**
     * @param LogEvent $event
     * @return bool
     */
    public function accept(LogEvent $event);

}
