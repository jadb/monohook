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
 * Test handler.
 *
 * @package Monohook
 * @subpackage Monohook.Handler
 * @author Jad Bitar <jadbitar@mac.com>
 */
class TestHandler extends AbstractHandler
{
    /**
     * Holds outputted messages.
     *
     * @var array
     */
    protected $output = array();

    /**
     * Asserts that message has been outputted.
     *
     * @param string $message Message to check for.
     * @return boolean
     */
    public function hasHandled($message)
    {
        return in_array($message, $this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function output($output)
    {
        $this->output = (array) $output;
    }
}
