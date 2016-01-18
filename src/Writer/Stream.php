<?php
/**
 * Logs input to a stream (Most common should be file)
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Writer
 */
class Stream extends AbstractWriter
{
    /**
     * @var mixed
     */
    protected $stream;

    /**
     * @var bool
     */
    protected $useLocks = false;

    /**
     * Open url/stream
     * @return void
     * @throws \RuntimeException
     */
    public function init()
    {
        $stream = @fopen($this->config['url'], 'a');
        if (!$stream) {
            throw new \RuntimeException(sprintf("Couldn't open resource '%s'", $this->config['url']));
        }

        $this->stream = $stream;

        if (!empty($this->config['lock']) && $this->config['lock'] === true && stream_supports_lock($this->stream)) {
            $this->useLocks = true;
        }
    }

    /**
     * Not sure if that's needed
     * @return void
     */
    public function __destruct()
    {
        @fclose($this->stream);
    }

    /**
     * @return mixed
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @param LogEvent $logEvent
     * @return void
     */
    protected function _write(LogEvent $logEvent)
    {
        $line = $this->formatter->format($logEvent);
        if ($this->useLocks === true) {
            flock($this->stream, LOCK_EX);
        }

        fwrite($this->stream, $line);

        if ($this->useLocks === true) {
            flock($this->stream, LOCK_UN);
        }
    }
}
