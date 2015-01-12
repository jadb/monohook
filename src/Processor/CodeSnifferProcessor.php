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
 * CodeSniffer processor.
 *
 * @package Monohook
 * @subpackage Monohook.Processor
 * @author Jad Bitar <jadbitar@mac.com>
 */
class CodeSnifferProcessor extends AbstractExecutableProcessor
{
    protected $executables = array(
        'php' => 'phpcs -n -s',
        'css' => 'phpcs -n -s',
        'js' => 'jscs'
    );

    /**
     * {@inheritdoc}
     */
    protected $extensions = array(
        'php',
        'css',
        'js',
    );

    /**
     * PHP Code Sniffer value to use with `--standard`. Check available standards by running
     * `phpcs -i`.
     *
     * @var string
     */
    protected $standards = array(
        'php' => 'PSR2',
        'css' => 'Squiz',
        'js' => 'Jquery',
    );

    /**
     * {@inheritdoc}
     */
    public function __invoke(ProviderInterface $Provider)
    {
        $Provider->stash();
        $this->output(sprintf("running code sniffer on all %s files", implode(', ', $this->extensions)), false);
        $ret = true;

        foreach ($Provider->files as $File) {
            if (!$File->isValid($this->extensions, $this->paths)) {
                continue;
            }

            $extension = $File->getExtension();

            if (!isset($files[$extension])) {
                $files[$extension] = array();
            }

            $files[$extension][] = $File->getPathname();
        }

        foreach ($this->extensions as $extension) {
            if (empty($files[$extension])) {
                continue;
            }

            $this->standard = $this->standards[$extension];
            $this->setExecutable($this->executables[$extension]);

            if (!$this->{$extension}($files[$extension])) {
                $ret = false;
                if ($this->halt) {
                    $Provider->unstash();
                    return $ret;
                }
            }
        }

        if ($ret) {
            $this->output("\t\t\tOK");
        }

        $Provider->unstash();
        return true;
    }

    protected function css($files)
    {
        return $this->php($files);
    }

    protected function php($files)
    {
        $cmd = sprintf('--standard=%s %s', $this->standard, implode(' ', array_map('escapeshellarg', $files)));
        $this->execute($cmd, $output, $return);

        if (0 !== $return) {
            $this->output($output);
            return false;
        }
        return true;
    }

    protected function js($files)
    {
        $cmd = sprintf('%s --standard=%s', implode(' ', array_map('escapeshellarg', $files)), $this->standard);
        $this->execute($cmd, $output, $return);

        if (0 !== $return) {
            $this->output($output);
            return false;
        }
        return true;
    }
}
