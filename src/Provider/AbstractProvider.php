<?php

/*
 * This file is part of the Monohook package.
 *
 * (c) Jad Bitar <jadbitar@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monohook\Provider;

use Monohook\File;

/**
 * Base Provider class to structure providers.
 *
 * @package Monohook
 * @subpackage Monohook.Provider
 * @author Jad Bitar <jadbitar@mac.com>
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * Provider's name.
     *
     * @var string
     */
    protected $name;

    /**
     * Cache stack to hold outputs.
     *
     * @var array
     */
    private $cache = array();

    /**
     * Stash indicator.
     *
     * @var boolean
     */
    private $stashed = false;

    /**
     * Getter.
     */
    public function __get($name)
    {
        switch ($name) {

            case 'code':
                return $this->getAddedCode();

            case 'files':
                $callback = function ($n) {
                    return new File($n);
                };
                return array_map($callback, $this->getAddedFiles());

            case 'head':
                return $this->getHead();

            case 'hook':
                return $this->getHook();

            case 'name':
                return $this->getName();

            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * Executes given command (for easier mocks).
     *
     * @param string $cmd Command to execute.
     * @param array $output If the output argument is present, then the specified array
     *              will be filled with every line of output from the command. Trailing
     *              whitespace, such as \n, is not included in this array. Note that if
     *              the array already contains some elements, exec() will append to the
     *              end of the array. If you do not want the function to append elements,
     *              call unset() on the array before passing it to exec().
     * @param int $return If this is present along with the output argument, then the
     *            return status of the executed command will be written to this variable.
     * @return void
     */
    public function execute($cmd, &$output = array(), &$return = 0, $cache = true)
    {
        if (!$cache || empty($this->cache[$cmd])) {
            exec($cmd, $output, $return);
            if ($cache) {
                $this->cache[$cmd] = compact('output', 'return');
            }
        }

        if ($cache) {
            extract($this->cache[$cmd]);
        }
    }

    /**
     * Returns all files (added, modified and untracked).
     */
    public function getAllFiles()
    {
        return array_merge($this->getAddedFiles(), $this->getModifiedFiles(), $this->getUntrackedFiles());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        if (empty($this->name)) {
            $this->name = strtolower(str_replace('Provider', '', __CLASS__));
        }

        return $this->name;
    }

    /**
     * Tells if a stash exists or not.
     *
     * @return boolean
     */
    public function isStashed()
    {
        return (bool) $this->stashed;
    }

    /**
     * {@inheritdoc}
     */
    public function stash()
    {
        if ($this->isStashed()) {
            return;
        }

        $tmp = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'monohookStash' . DIRECTORY_SEPARATOR;

        # Copy files to tmp
        foreach ($this->getAllFiles() as $file) {
            $dir = dirname($file);
            if ('.' == $dir) {
                $dir = '';
            } else {
                $dir .= DIRECTORY_SEPARATOR;
            }

            $tmpDir = $tmp . $dir;

            if (is_dir($tmpDir)) {
                $this->execute('rm -rf ' . $this->stashed);
            }

            $this->execute('mkdir -p ' . $tmpDir, $output, $return, false);
            $this->execute(sprintf('cp %s %s', $file, $tmp . $dir . basename($file)), $output, $return, false);
        }

        $this->clean();

        $this->stashed = $tmp;
    }

    /**
     * {@inheritdoc}
     */
    public function unstash()
    {
        if (!$this->isStashed()) {
            return;
        }

        /*
            Copy stashed (copied) files back to working directory.
         */
        $this->execute(sprintf('cp -r %s. .', $this->stashed), $output, $return, false);

        /*
            Delete stashed files.
         */
        $this->execute('rm -rf ' . $this->stashed);
        $this->stashed = false;
    }
}
