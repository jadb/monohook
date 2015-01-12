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
use Monohook\Processor\TestProcessor;
use Monohook\Provider\TestProvider;

/**
 * Monohook test.
 *
 * @author Jad Bitar <jadbitar@mac.com>
 */
class MonohookTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $Handler = $this->getMock('\Monohook\Handler\TestHandler');
        $Provider = $this->getMock('\Monohook\Provider\TestProvider');

        $Processor = $this->getMock(
            '\Monohook\Processor\TestProcessor',
            array('__invoke'),
            array($Handler)
        );

        $Monohook = $this->getMock(
            '\Monohook\Monohook',
            array('__destruct'),
            array($Provider, array(array($Processor)))
        );

        $Processor->expects($this->once())
            ->method('__invoke')
            ->with($Provider);

        $Monohook->run();
    }
}
