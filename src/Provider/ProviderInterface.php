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
 * Provider interface.
 *
 * @package Monohook
 * @subpackage Monohook.Provider
 * @author Jad Bitar <jadbitar@mac.com>
 */
interface ProviderInterface
{
    /**
     * Force working directory to have a clean state. Don't trigger callbacks, see GitProvider.
     *
     * @return void
     */
    public function clean();

    /**
     * Gets the new code to be committed.
     *
     * @return string
     */
    public function getAddedCode();

    /**
     * Gets the list of files to be committed.
     *
     * @return array
     */
    public function getAddedFiles();

    /**
     * Gets all the modified files that haven't been added to the index.
     *
     * @return array
     */
    public function getModifiedFiles();

    /**
     * Gets all files that aren't tracked yet by VCS, EXCLUDING the ignored ones.
     *
     * @return array
     */
    public function getUntrackedFiles();

    /**
     * Gets the repository's HEAD.
     *
     * @return string
     */
    public function getHead();

    /**
     * Gets the hook's name (i.e. pre-commit).
     *
     * @return string
     */
    public function getHook();

    /**
     * Gets the provider's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Tells if repository is shallow (no commits yet) or not.
     *
     * @return boolean
     */
    public function isShallow();

    /**
     * Save work that is not committed and put aside before running processors.
     *
     * @return void
     */
    public function stash();

    /**
     * Retrieve saved work (if any).
     *
     * @return void
     */
    public function unstash();
}
