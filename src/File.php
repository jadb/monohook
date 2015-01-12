<?php

/*
 * This file is part of the Monohook package.
 *
 * (c) Jad Bitar <jadbitar@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monohook;

/**
 * File object.
 *
 * @package Monohook
 * @author Jad Bitar <jadbitar@mac.com>
 */
class File extends \SplFileInfo
{
    public function hasExtension($extensions, $escape = true)
    {
        foreach ((array) $extensions as $extension) {
            if ($escape) {
                $extension = preg_quote($extension);
            }

            if (preg_match(sprintf('@%s$@', $extension), $this->getFilename())) {
                return true;
            }
        }

        return false;
    }

    public function hasPath($paths, $escape = true)
    {
        foreach ((array) $paths as $path) {
            if (substr($path, -1) != DIRECTORY_SEPARATOR) {
                $path .= DIRECTORY_SEPARATOR;
            }

            if ($escape) {
                $path = preg_quote($path);
            }

            if (preg_match(sprintf('@^%s@', $path), $this->getPathname())) {
                return true;
            }
        }

        return empty($paths);
    }

    public function isValid($extensions, $paths)
    {
        return $this->isFile()
            && $this->hasExtension($extensions)
            && $this->hasPath($paths)
        ;
    }
}
