<?php namespace Tests\Stubs;

/**
 * Class PhpStream
 * @package Tests\Stubs
 *
 * This is not a full implementation of a stream replacement.
 *
 * It is just the minimum required to mock the php stream for testing.
 */
class PhpStream
{
    protected $index = 0;
    protected $length = null;
    protected $data = '';

    public $context;

    /**
     * PhpStream constructor.
     */
    function __construct()
    {
        if (file_exists($this->buffer_filename())) {
            $this->data = file_get_contents($this->buffer_filename());
        }

        $this->index = 0;
        $this->length = strlen($this->data);
    }

    /**
     * Get buffer filename.
     *
     * @return string
     */
    protected function buffer_filename()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php_input.txt';
    }

    /**
     * Seek stream
     *
     * @param $offset
     * @param $whence
     * @return bool
     */
    public function stream_seek($offset, $whence) {
        $this->index = 0;

        return true;
    }

    /**
     * Stream tell.
     *
     * @return int
     */
    public function stream_tell() {
        return $this->index;
    }

    /**
     * Open stream.
     *
     * @param $path
     * @param $mode
     * @param $options
     * @param $opened_path
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        return true;
    }

    /**
     * Close stream
     */
    public function stream_close()
    {
    }

    /**
     * Get stream stat.
     *
     * @return array
     */
    public function stream_stat()
    {
        return [];
    }

    /**
     * Flush stream.
     *
     * @return bool
     */
    public function stream_flush()
    {
        return true;
    }

    /**
     * Read stream.
     *
     * @param $count
     * @return bool|string
     */
    public function stream_read($count)
    {
        if (is_null($this->length) === true) {
            $this->length = strlen($this->data);
        }

        $length = min($count, $this->length - $this->index);
        $data = substr($this->data, $this->index);
        $this->index = $this->index + $length;

        return $data;
    }

    /**
     * EOF stream.
     *
     * @return bool
     */
    public function stream_eof()
    {
        return ($this->index >= $this->length ? true : false);
    }

    /**
     * Write stream.
     *
     * @param $data
     * @return bool|int
     */
    public function stream_write($data)
    {
        return file_put_contents($this->buffer_filename(), $data);
    }

    /**
     * Unlink
     */
    public function unlink()
    {
        if (file_exists($this->buffer_filename())) {
            unlink($this->buffer_filename());
        }

        $this->data = '';
        $this->index = 0;
        $this->length = 0;
    }
}