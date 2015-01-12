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

use Monohook\Processor\RebaseProcessor;

/**
 * Rebase processor test.
 *
 * @author Jad Bitar <jadbitar@mac.com>
 */
class RebaseProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $methods = array('execute', 'getHook', 'getName', 'isShallow');
        $Provider = $this->getMock('\Monohook\Provider\TestProvider', $methods);
        $Handler = $this->getMock('\Monohook\Handler\TestHandler');
        $Processor = new RebaseProcessor($Handler);

        $rebaseBranch = 'develop';
        $topicBranch = 'feature/test';
        $_SERVER['argv'] = array($rebaseBranch, $topicBranch);

        $Provider->expects($this->once())
            ->method('isShallow')
            ->with()
            ->will($this->returnValue(false));

        $Provider->expects($this->once())
            ->method('getName')
            ->with()
            ->will($this->returnValue('git'));

        $Provider->expects($this->once())
            ->method('getHook')
            ->with()
            ->will($this->returnValue('pre-rebase'));

        $Provider->expects($this->at(3))
            ->method('execute')
            ->with("git rev-list $rebaseBranch..refs/heads/$topicBranch")
            ->will($this->returnValue("sha1"));

        $Provider->expects($this->at(4))
            ->method('execute')
            ->with('git branch -r')
            ->will($this->returnValue("origin/HEAD\norigin/develop"));

        $Provider->expects($this->at(5))
            ->method('execute')
            ->with('git rev-list ^sha1^@ refs/remotes/origin/HEAD')
            ->will($this->returnValue(''));

        $Provider->expects($this->at(6))
            ->method('execute')
            ->with('git rev-list ^sha1^@ refs/remotes/origin/develop')
            ->will($this->returnValue(''));

        $Handler->expects($this->once())
            ->method('write')
            ->with("Rebase of $rebaseBranch does not overwrite any of refs/heads/$topicBranch remote commits\n");

        call_user_func($Processor, $Provider);
    }

    public function testInvokeNoTopicBranch()
    {
        $methods = array('execute', 'getHook', 'getName', 'isShallow');
        $Provider = $this->getMock('\Monohook\Provider\TestProvider');
        $Handler = $this->getMock('\Monohook\Handler\TestHandler');
        $Processor = new RebaseProcessor($Handler);

        $rebaseBranch = 'develop';
        $topicBranch = 'refs/heads/feature/test';
        $_SERVER['argv'] = array($rebaseBranch);

        $Provider->expects($this->once())
            ->method('isShallow')
            ->with()
            ->will($this->returnValue(false));

        $Provider->expects($this->once())
            ->method('getName')
            ->with()
            ->will($this->returnValue('git'));

        $Provider->expects($this->once())
            ->method('getHook')
            ->with()
            ->will($this->returnValue('pre-rebase'));

        $Provider->expects($this->at(3))
            ->method('execute')
            ->with('git symbolic-ref HEAD')
            ->will($this->returnValue($topicBranch));

        $Provider->expects($this->at(4))
            ->method('execute')
            ->with("git rev-list $rebaseBranch..$topicBranch")
            ->will($this->returnValue("sha1"));

        $Provider->expects($this->at(5))
            ->method('execute')
            ->with('git branch -r')
            ->will($this->returnValue("origin/HEAD\norigin/develop"));

        $Provider->expects($this->at(6))
            ->method('execute')
            ->with('git rev-list ^sha1^@ refs/remotes/origin/HEAD')
            ->will($this->returnValue(''));

        $Provider->expects($this->at(7))
            ->method('execute')
            ->with('git rev-list ^sha1^@ refs/remotes/origin/develop')
            ->will($this->returnValue(''));

        $Handler->expects($this->once())
            ->method('write')
            ->with("Rebase of $rebaseBranch does not overwrite any of $topicBranch remote commits\n");

        call_user_func($Processor, $Provider);
    }

    public function testInvokeOnShallowRepository()
    {
        $Provider = $this->getMock('\Monohook\Provider\TestProvider');
        $Handler = $this->getMock('\Monohook\Handler\TestHandler');
        $Processor = new RebaseProcessor($Handler);

        $Provider->expects($this->once())
            ->method('isShallow')
            ->with()
            ->will($this->returnValue(true));

        $Provider->expects($this->never())->method('execute');
        $Handler->expects($this->never())->method('write');

        call_user_func($Processor, $Provider);
    }
}
