<?php
/**
 * Logs input to file
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

class FileWriter extends AbstractWriter {

    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * Check if file exists and is writable
     * @return AbstractWriter
     */
    public function init() {
        $filePath = $this->config[__CLASS__]['file'];
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
