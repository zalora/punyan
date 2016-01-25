<?php
/**
 * Wrapper for $GLOBALS for unit tests, taken php online documentation
 * I only made the code a bit more PHP5ish, despite the PHPDoc I'm not the author of the code
 * @link http://php.net/manual/en/stream.streamwrapper.example-1.php
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\StreamWrapper;

/**
 * @package StreamWrapper
 * @codeCoverageIgnore
 */
class VariableStream {

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $varname;

    /**
     * @param string $path
     * @param string $mode
     * @param array $options
     * @param string $opened_path
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->varname = $url['host'];
        $this->position = 0;
        $GLOBALS[$this->varname] = '';
        return true;
    }

    /**
     * @param int $count
     * @return string
     */
    public function stream_read($count)
    {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    /**
     * @param string $data
     * @return int
     */
    public function stream_write($data)
    {
        $left = substr($GLOBALS[$this->varname], 0, $this->position);
        $right = substr($GLOBALS[$this->varname], $this->position + strlen($data));
        $GLOBALS[$this->varname] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    /**
     * @return int
     */
    public function stream_tell()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function stream_eof()
    {
        return $this->position >= strlen($GLOBALS[$this->varname]);
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return bool
     */
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($GLOBALS[$this->varname]) && $offset >= 0) {
                    $this->position = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->position += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen($GLOBALS[$this->varname]) + $offset >= 0) {
                    $this->position = strlen($GLOBALS[$this->varname]) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }

    /**
     * @param string $path
     * @param array $option
     * @param mixed $var
     * @return bool
     */
    public function stream_metadata($path, $option, $var)
    {
        if($option == STREAM_META_TOUCH) {
            $url = parse_url($path);
            $varname = $url['host'];
            if(!isset($GLOBALS[$varname])) {
                $GLOBALS[$varname] = '';
            }
            return true;
        }
        return false;
    }

    /**
     * @param $operation
     * @return bool
     */
    public function stream_lock($operation)
    {
        return true;
    }

    /**
     * @return array
     */
    public function stream_stat()
    {
        return array();
    }
}
