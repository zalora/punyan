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
    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public function fatal($msg, array $context = [])
    {
        $context['class'] = __CLASS__;
        LoggerProxy::fatal($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public function error($msg, array $context = [])
    {
        $context['class'] = __CLASS__;
        LoggerProxy::error($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public function warn($msg, array $context = [])
    {
        $context['class'] = __CLASS__;
        LoggerProxy::warn($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public function info($msg, array $context = [])
    {
        $context['class'] = __CLASS__;
        LoggerProxy::info($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public function debug($msg, array $context = [])
    {
        $context['class'] = __CLASS__;
        LoggerProxy::debug($msg, $context);
    }

    /**
     * @param string|\Exception $msg
     * @param array $context
     * @throws \RuntimeException
     */
    public function trace($msg, array $context = [])
    {
        $context['class'] = __CLASS__;
        LoggerProxy::trace($msg, $context);
    }
}
