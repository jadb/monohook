<?php

/*
 * This file is part of the Monohook package.
 *
 * (c) Jad Bitar <jadbitar@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monohook\Processor;

use Monohook\Handler\HandlerInterface;
use Monohook\Handler\StdoutHandler;

/**
 * Base Processor class to structure providers.
 *
 * @package Monohook
 * @subpackage Monohook.Processor
 * @author Jad Bitar <jadbitar@mac.com>
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    const FAIL = 1;
    const PASS = 0;

    /**
     * File extensions. Skips any file that doesn't match.
     *
     * @var array
     */
    protected $extensions;

    /**
     * Tells whether to halt hook execution or not.
     *
     * @var boolean
     */
    protected $halt;

    /**
     * Handler.
     *
     * @var HandlerInterface
     */
    protected $Handler;

    /**
     * Defines source files path(s).
     *
     * @var array
     */
    protected $paths;

    /**
     * Constructor.
     *
     * @param mixed $handler Handler object to use or null.
     * @param boolean $verbose Verbose mode.
     */
    public function __construct(HandlerInterface $Handler, $halt = true)
    {
        $this->Handler = $Handler;

        if (is_array($halt)) {
            $config = array_merge(array('halt' => true), $halt);
        } else {
            $config = compact('halt');
        }


        foreach ($config as $property => $value) {
            if (property_exists($this, $property)) {
                if (in_array($property, array('extensions', 'paths'))) {
                    $value = (array) $value;
                }
                $this->$property = $value;
            }
        }
    }

    public function output($message, $newline = true)
    {
        if ($newline) {
            $message = implode("\n", (array) $message) . "\n";
        }

        $this->Handler->write($message);
    }
}
