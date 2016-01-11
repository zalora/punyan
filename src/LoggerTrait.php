<?php
/**
 * Static wrapper for logger instances
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

/**
 * Acts as a proxy to the real adapters
 * @package Zalora\Punyan
 */
trait LoggerTrait
{
    public function warn($msg, array $context = array()) {
        $context['class'] = __CLASS__;
        LoggerProxy::warn($msg, $context);
    }
}
