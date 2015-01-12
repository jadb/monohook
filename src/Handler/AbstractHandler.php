<?php

/*
 * This file is part of the Monohook package.
 *
 * (c) Jad Bitar <jadbitar@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monohook\Handler;

/**
 * Base Handler class to structure handlers.
 *
 * @package Monohook
 * @subpackage Monohook.Handler
 * @author Jad Bitar <jadbitar@mac.com>
 */
abstract class AbstractHandler implements HandlerInterface
{
    const QUIET = false;
    const VERBOSE = true;

    /**
     * Tells if handler needs to keep messages and dump them all at once.
     *
     * @var boolean
     */
    protected $bubble;

    /**
     * Buffered messages stack.
     *
     * @var array
     */
    protected $records = array();

    /**
     * Tells if should (not) skip outputting anything.
     *
     * @var boolean
     */
    protected $verbose;

    /**
     * Constructor.
     *
     * @param boolean $bubble Tells if handler needs to keep messages and dump them all at once.
     * @param boolean $verbose Tells if should (not) skip outputting anything.
     */
    public function __construct($bubble = false, $verbose = self::VERBOSE)
    {
        $this->bubble = $bubble;
        $this->verbose = $verbose;
    }

    /**
     * Dump all messages.
     *
     * @return void
     */
    public function dump()
    {
        if (empty($this->records)) {
            return;
        }

        $this->handleBatch();
        $this->records = array();
    }

    /**
     * Outputs message(s).
     *
     * @param mixed $output A string when a single message is passed or an array when buffering.
     * @return void
     * @throws MissingMethodException If this method is not defined in the child class.
     */
    public function output($output)
    {
        throw new \BadMethodCallException();
    }

    /**
     * {@inheritdoc}
     */
    public function write($message)
    {
        if ($this->verbose === self::VERBOSE) {
            $this->record($message);
        }
    }

    /**
     * Handles single message.
     *
     * @param string $message Message to output.
     * @return void
     */
    protected function handle($message)
    {
        $this->output($message);
    }

    /**
     * Handles messages' stack.
     *
     * @return void
     */
    protected function handleBatch()
    {
        $this->output($this->records);
    }

    /**
     * Decides on whether to handle or buffer the message.
     *
     * @param string $message Message to output.
     * @return void
     */
    protected function record($message)
    {
        if (!$this->bubble) {
            $this->handle($message);
            return;
        }

        $this->records[] = $message;
    }
}
