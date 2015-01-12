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

use Monohook\Handler\TestHandler;

/**
 * Test handler test.
 *
 * @author Jad Bitar <jadbitar@mac.com>
 */
class TestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        $handler = new TestHandler();
        $handler->write('This is a test');
        $this->assertTrue($handler->hasHandled('This is a test'));

        $handler = new TestHandler(false, TestHandler::QUIET);
        $handler->write('This is a test');
        $this->assertFalse($handler->hasHandled('This is a test'));
    }

    public function testDump()
    {
        $handler = new TestHandler(true);
        $handler->write('This is a test');
        $this->assertFalse($handler->hasHandled('This is a test'));
        $handler->dump();
        $this->assertTrue($handler->hasHandled('This is a test'));

        $handler = new TestHandler(true, TestHandler::QUIET);
        $handler->write('This is a test');
        $this->assertFalse($handler->hasHandled('This is a test'));
        $handler->dump();
        $this->assertFalse($handler->hasHandled('This is a test'));
    }
}
