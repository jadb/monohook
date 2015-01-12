<?php

/*
 * This file is part of the Monohook package.
 *
 * (c) Jad Bitar <jadbitar@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monohook\Test;

use Monohook\Handler\StdoutHandler;

/**
 * Output handler test.
 *
 * @author Jad Bitar <jadbitar@mac.com>
 */
class StdoutHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        $this->expectOutputString('This is a test');

        $handler = new StdoutHandler();
        $handler->write('This is a test');
    }

    public function testWriteBubble()
    {
        $this->expectOutputString(null);

        $handler = new StdoutHandler(true);
        $handler->write('This is a test');
    }

    public function testWriteQuiet()
    {
        $this->expectOutputString(null);

        $handler = new StdoutHandler(false, StdoutHandler::QUIET);
        $handler->write('This is a test');
    }

    public function testDump()
    {
        $this->expectOutputString(implode("\n", array('This is a test', 'This is another test')));

        $handler = new StdoutHandler(true);
        $handler->write('This is a test');
        $handler->write('This is another test');
        $handler->dump();
    }

    public function testDumpQuiet()
    {
        $this->expectOutputString(null);

        $handler = new StdoutHandler(true, StdoutHandler::QUIET);
        $handler->write('This is a test');
        $handler->write('This is another test');
        $handler->dump();
    }
}
