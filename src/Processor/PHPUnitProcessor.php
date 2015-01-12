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

use Monohook\Provider\ProviderInterface;

/**
 * Code filter processor.
 *
 * @package Monohook
 * @subpackage Monohook.Processor
 * @author Jad Bitar <jadbitar@mac.com>
 */
class PHPUnitProcessor extends AbstractExecutableProcessor
{
    /**
     * Automatically look for committed files' associated tests or not.
     *
     * @var boolean
     */
    protected $autoSearch = true;

    /**
     * {@inheritdoc}
     */
    protected $executable = 'phpunit --stop-on-failure';

    /**
     * {@inheritdoc}
     */
    protected $extensions = array(
        'php',
    );

    /**
     * Tests location(s). Skips any directory that doesn't match.
     *
     * @var array
     */
    protected $testDirectories = array(
        'test',
        'tests',
        'Test',
        'Tests',
    );

    /**
     * Tests suffix(es). Skips any file that doesn't match.
     *
     * @var array
     */
    protected $testSuffixes = array(
        '.test.php',
        'Test.php',
    );

    /**
     * {@inheritdoc}
     */
    public function __invoke(ProviderInterface $Provider)
    {
        $Provider->stash();
        $this->output(sprintf("running phpunit tests on all %s files", implode(', ', $this->extensions)), false);
        $passes = $failures = array();
        $ret = true;

        foreach ($Provider->files as $File) {
            if (!$File->isValid($this->extensions, $this->paths)) {
                continue;
            }

            $test = false;
            if ($this->isTestFile($File)) {
                $test = $File->getPathname();
            } elseif ($this->autoSearch) {
                $test = $this->findTestFor($File);
            }

            if (!$test || in_array($test, $passes) || in_array($test, $failures)) {
                continue;
            }

            $cmd = escapeshellarg($test);
            $this->execute($cmd, $output, $return);

            if (0 != $return) {
                array_push($failures, $test);
                $this->output($output, true);
                $ret = false;
                if ($this->halt) {
                    $Provider->unstash();
                    return $ret;
                }
            } else {
                array_push($passes, $test);
            }
        }

        if ($ret) {
            $this->output("\t\t\t\tOK");
        }

        $Provider->unstash();
        return true;
    }

    /**
     * Finds test for $file by looping through all supported test directories.
     *
     * @param File $File File to get test for.
     * @return string Test file.
     */
    public function findTestFor($File)
    {
        $ds = DIRECTORY_SEPARATOR;
        $test = null;
        $path = explode($ds, $File->getPathname());
        $filename = array_pop($path);
        $extension = '.' . $File->getExtension();
        $checked = array();

        foreach ($this->testSuffixes as $suffix) {
            $test = implode($ds, $path) . $ds;
            $test .= substr($filename, 0, -strlen($extension)) . $suffix;
            if (!in_array($test, $checked) && file_exists($test)) {
                array_push($checked, $test);
                return $test;
            }
            array_push($checked, $test);
        }

        foreach ($this->testPaths($path) as $testPath) {
            foreach ($this->testSuffixes as $suffix) {
                $test = $testPath . substr($filename, 0, -strlen($extension)) . $suffix;
                if (!in_array($test, $checked) && file_exists($test)) {
                    array_push($checked, $test);
                    return $test;
                }
                array_push($checked, $test);
            }
        }

        if (!$test || !file_exists($test)) {
            return false;
        }

        return $test;
    }

    /**
     * Checks if $file is a test file.
     *
     * @param string $file File to check.
     * @return boolean
     */
    public function isTestFile($File)
    {
        return $File->hasExtension($this->testSuffixes);
    }

    /**
     * Adds $element to $stack only if it doesn't already exist.
     *
     * @param array $stack Elements' stack.
     * @param mixed $element Element to add.
     * @return array
     */
    public function push($stack, $element)
    {
        if (!in_array($element, $stack)) {
            array_push($stack, $element);
        }
        return $stack;
    }

    /**
     * Generate path(s) where to search for test(s).
     *
     * @param array $parts
     * @return array Test path(s).-0
     */
    public function testPaths($parts)
    {
        $ds = DIRECTORY_SEPARATOR;
        $testPaths = array();

        for ($i = count($parts) + 1; $i >= 0; $i--) {
            foreach ($this->testDirectories as $directory) {
                $testPath = $parts;
                array_splice($testPath, $i, 0, $directory);
                $testPath = implode($ds, $testPath) . $ds;
                $testPaths = $this->push($testPaths, $testPath);

                if ($i > 0) {
                    continue;
                }

                foreach ($this->paths as $source) {
                    $regex = sprintf('@%s@', preg_quote($source. $ds));
                    $testPaths = $this->push($testPaths, preg_replace($regex, '', $testPath, 1));
                }
            }
        }

        return array_values(array_filter($testPaths, 'is_dir'));
    }
}
