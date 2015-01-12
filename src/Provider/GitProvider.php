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

/**
 * Git provider.
 *
 * @package Monohook
 * @subpackage Monohook.Provider
 * @author Jad Bitar <jadbitar@mac.com>
 */
class GitProvider extends AbstractProvider
{
    /**
     * Fake SHA when HEAD is not available.
     *
     * @var string
     */
    const HEAD = '4b825dc642cb6eb9a060e54bf8d69288fbee4904';

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        if (!$this->isShallow()) {
            /*
                Not using the `git checkout .` since it would trigger the `post-checkout` hook and
                cause infinite loops in certain cases. Instead, using the `git stash` followed by a
                `git stash drop`.

                Another workaround was also needed to temporarily delete untracked files except the
                ones that match a `.gitignore` rule (i.e. Composer's vendor directories for example).

                Could have used `git stash --quiet --keep-index --include-untracked` which according to
                its description should do exactly that but instead it runs as if the `--all` option was
                passed and deletes stuff that should be ignored.
             */
            $this->execute('git stash --quiet --keep-index && git stash drop && git clean -f');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAddedCode()
    {
        $head = $this->getHead();
        $output = '';
        $this->execute('git diff-index --cached ' . $head, $output);

        if (!$output && $head === 'HEAD') {
            $this->execute('git diff-index --cached HEAD^', $output);
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddedFiles()
    {
        $head = $this->getHead();
        $output = array();
        $this->execute('git diff-index --cached --name-only ' . $head, $output);

        if (!$output && $head === 'HEAD') {
            $this->execute('git diff-index --cached --name-only HEAD^', $output);
        }

        return array_filter($output, 'file_exists');
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedFiles()
    {
        $head = $this->getHead();
        $output = array();
        $this->execute('git diff --name-only ' . $head, $output);

        if (!$output && $head === 'HEAD') {
            $this->execute('git diff --name-only HEAD^', $output);
        }

        return array_filter(array_diff($output, $this->getAddedFiles()), 'file_exists');
    }

    /**
     * {@inheritdoc}
     */
    public function getUntrackedFiles()
    {
        $head = $this->getHead();
        $output = array();
        $this->execute('git ls-files --others --exclude-standard', $output);

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getHead()
    {
        $this->execute('git rev-parse --verify HEAD 2> /dev/null', $output, $return);
        $head = self::HEAD;
        if (0 === $return) {
            $head = 'HEAD';
        }
        return $head;
    }

    /**
     * {@inheritdoc}
     */
    public function getHook()
    {
        return str_replace('.git/hooks/', '', $_SERVER['SCRIPT_FILENAME']);
    }

    /**
     * {@inheritdoc}
     */
    public function isShallow()
    {

        return $this->getHead() == self::HEAD;
    }
}
