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

use Monohook\Provider\GitProvider;

/**
 * Git provider test.
 *
 * @author Jad Bitar <jadbitar@mac.com>
 */
class GitProviderTest extends \PHPUnit_Framework_TestCase
{
    const PROVIDER = 'Monohook\Provider\GitProvider';

    public function testGetAddedCode()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute', 'getHead'));

        $provider->expects($this->at(0))
            ->method('getHead')
            ->with()
            ->will($this->returnValue('HEAD'));

        $provider->expects($this->at(1))
            ->method('execute')
            ->with('git diff-index --cached HEAD', '');

        $provider->expects($this->at(2))
            ->method('execute')
            ->with('git diff-index --cached HEAD^', '');

        $result = $provider->getAddedCode();
        $this->assertTrue(is_string($result));

    }

    public function testGetAddedCodeShallowRepository()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute', 'getHead'));

        $provider->expects($this->once())
            ->method('getHead')
            ->with()
            ->will($this->returnValue(GitProvider::HEAD));

        $provider->expects($this->once())
            ->method('execute')
            ->with('git diff-index --cached ' . GitProvider::HEAD, '');

        $result = $provider->getAddedCode();
        $this->assertTrue(is_string($result));
    }

    public function testGetAddedFiles()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute', 'getHead'));

        $provider->expects($this->at(0))
            ->method('getHead')
            ->with()
            ->will($this->returnValue('HEAD'));

        $provider->expects($this->at(1))
            ->method('execute')
            ->with('git diff-index --cached --name-only HEAD', array());

        $provider->expects($this->at(2))
            ->method('execute')
            ->with('git diff-index --cached --name-only HEAD^', array());

        $result = $provider->getAddedFiles();
        $this->assertTrue(is_array($result));
    }

    public function testGetAddedFilesShallowRepository()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute', 'getHead'));

        $provider->expects($this->once())
            ->method('getHead')
            ->with()
            ->will($this->returnValue(GitProvider::HEAD));

        $provider->expects($this->once())
            ->method('execute')
            ->with('git diff-index --cached --name-only ' . GitProvider::HEAD, array());

        $result = $provider->getAddedFiles();
        $this->assertTrue(is_array($result));
    }

    public function testGetHead()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute'));

        $provider->expects($this->once())
            ->method('execute')
            ->with('git rev-parse --verify HEAD 2> /dev/null', null, null);

        $result = $provider->getHead();
        $expected = GitProvider::HEAD;
        $this->assertEquals($expected, $result);
    }

    public function testGetHeadOfShallowRepository()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute'));

        $provider->expects($this->once())
            ->method('execute');

        $result = $provider->getHead();
        $this->assertEquals($result, GitProvider::HEAD);
    }

    public function testGetHook()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute'));

        $_SERVER['SCRIPT_FILENAME'] = '.git/hooks/pre-commit';

        $result = $provider->getHook();
        $expected = 'pre-commit';
        $this->assertEquals($expected, $result);
    }

    public function testIsShallow()
    {
        $provider = $this->getMock(self::PROVIDER, array('execute', 'getHead'));

        $provider->expects($this->exactly(2))
            ->method('getHead')
            ->with()
            ->will($this->onConsecutiveCalls(GitProvider::HEAD, ''));

        $this->assertTrue($provider->isShallow());
        $this->assertFalse($provider->isShallow());
    }

    public function testStash()
    {
        $this->markTestIncomplete();
    }

    public function testStashOfShallowRepository()
    {
        $this->markTestIncomplete();
    }

    public function testUnstash()
    {
        $this->markTestIncomplete();
    }

    public function testUnstashOfShallowRepository()
    {
        $this->markTestIncomplete();
    }
}
