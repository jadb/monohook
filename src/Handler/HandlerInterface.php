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
 * Handler interface.
 *
 * @package Monohook
 * @subpackage Monohook.Handler
 * @author Jad Bitar <jadbitar@mac.com>
 */
interface HandlerInterface
{
    /**
     * Writes message.
     *
     * @param string $message Message to output.
     * @return void
     */
    public function write($message);
}
