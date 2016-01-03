<?php
/**
 * Logs input to file
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
     * Open url/stream
     * @return void
     * @throws RuntimeException
     */
    public function init()
    {
        $stream = @fopen($this->config['url'], 'a');
        if (!$stream) {
            throw new RuntimeException(sprintf("Couldn't open resource '%s'", $this->config['url']));
        }
        $this->stream = $stream;
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
     * @param LogEvent $logEvent
     * @return void
     */
    protected function _write(LogEvent $logEvent)
    {
        $line = $this->formatter->format($logEvent);
        fwrite($this->stream, $line);
    }
}
