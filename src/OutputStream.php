<?php
/**
 * @copyright 2018 Creative Cow Limited
 */
declare(strict_types=1);

namespace Cc\OutputStream;

use Zend\Diactoros\Stream;

class OutputStream extends Stream
{

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var bool
     */
    protected $callbackStarted = false;

    /**
     * @var int
     */
    protected $childProcessId;

    /**
     * @inheritdoc
     */
    public function __construct($stream, callable $callback)
    {
        ob_implicit_flush(1);
        parent::__construct($stream);
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function eof(): bool
    {
        $this->spawnChildProcess();
        return posix_getpgid($this->childProcessId) === false;
    }

    /**
     * @inheritdoc
     */
    public function read($length): string
    {
        $this->spawnChildProcess();
        return parent::read($length);
    }

    /**
     * @inheritdoc
     */
    public function getContents(): string
    {
        throw new \RuntimeException('Output stream cannot get contents. Must be read whilst not end of file.');
    }

    /**
     * Spawn child process to run callback
     *
     * @return void
     */
    protected function spawnChildProcess(): void
    {
        if ($this->callbackStarted) {
            return;
        }
        $this->callbackStarted = true;

        /**
         * Make sure to send the headers before spawning the child process or
         * they will get sent by php in the child when it exits.
         */
        flush();

        $pid = pcntl_fork();
        if ($pid === -1) {
            throw new \RuntimeException('Unable to fork process.');
        }

        if ($pid === 0) {
            call_user_func($this->callback);
            exit;
        }

        $this->childProcessId = $pid;
    }
}
