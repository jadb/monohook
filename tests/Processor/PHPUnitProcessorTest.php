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

use Monohook\File;
use Monohook\Processor\PHPUnitProcessor;

/**
 * PHPUnit processor test.
 *
 * @author Jad Bitar <jadbitar@mac.com>
 */
class PHPUnitProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->Handler = $this->getMock('\Monohook\Handler\TestHandler');
        $this->Processor = new PHPUnitProcessor($this->Handler, array('paths' => array('src')));
    }

    public function testBuildPaths()
    {
        $result = $this->Processor->testPaths(['src']);
        $expected = array('tests/', 'Tests/');
        $this->assertEquals($expected, $result);

        $result = $this->Processor->testPaths(explode('/', 'src/Processor'));
        $expected = array('tests/Processor/', 'Tests/Processor/');
        $this->assertEquals($expected, $result);
    }

    public function testFindTestFor()
    {
        $result = $this->Processor->findTestFor(new File('src/Monohook.php'));
        $expected = 'tests/MonohookTest.php';
        $this->assertEquals($expected, $result);

        $result = $this->Processor->findTestFor(new File('src/Processor/PHPUnitProcessor.php'));
        $expected = 'tests/Processor/PHPUnitProcessorTest.php';
        $this->assertEquals($expected, $result);
    }

    public function testPush()
    {
        $stack = array('foo', 'bar', 'Foo');
        $result = $this->Processor->push($stack, 'foo');
        $this->assertEquals($stack, $result);
    }

    public function testIsTestFile()
    {
        $this->assertFalse($this->Processor->isTestFile(new File('Foo/Bar.php')));
        $this->assertFalse($this->Processor->isTestFile(new File('Foo/Bartest.php')));

        $this->assertTrue($this->Processor->isTestFile(new File('Foo/Bar.test.php')));
        $this->assertTrue($this->Processor->isTestFile(new File('Foo/BarTest.php')));
    }

    public function testTestPaths()
    {
        $this->markTestIncomplete();
    }
}
