<?php
/**
 * Logs input to file
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

class Stream extends AbstractWriter {

    /**
     * @var mixed
     */
    protected $stream;

    /**
     * Check if file exists and is writable
     * @return AbstractWriter
     */
    public function init() {
        $url = $this->config['url'];

        return $this;
    }

    /**
     * @param int $level
     * @param string $msg
     * @param array $context
     * @return void
     */
    protected function _write($level, $msg, array $context = array())
    {

    }
}
