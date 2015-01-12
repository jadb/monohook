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
 * Output handler.
 *
 * @package Monohook
 * @subpackage Monohook.Handler
 * @author Jad Bitar <jadbitar@mac.com>
 */
class StdoutHandler extends AbstractHandler
{
    /**
     * {@inheritdoc}
     */
    public function output($output)
    {
        print implode("\n", (array) $output);
    }
}
