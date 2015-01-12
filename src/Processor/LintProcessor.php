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
 * Lint processor.
 *
 * @package Monohook
 * @subpackage Monohook.Processor
 * @author Jad Bitar <jadbitar@mac.com>
 */
class LintProcessor extends AbstractExecutableProcessor
{

    /**
     * Executable by file type.
     *
     * @var array
     */
    protected $executables = array(
        'js' => 'jslint -p',
        'php' => 'php -l',
    );

    /**
     * {@inheritdoc}
     */
    protected $extensions = array(
        'js',
        'php',
    );

    /**
     * {@inheritdoc}
     */
    public function __invoke(ProviderInterface $Provider)
    {
        $this->output(sprintf("running lint syntax check on all %s files", implode(', ', $this->extensions)), false);
        $ret = true;

        foreach ($Provider->files as $File) {
            if (!$File->isValid($this->extensions, $this->paths)) {
                continue;
            }

            $this->setExecutable($this->executables[$File->getExtension()]);
            $this->execute(escapeshellarg($File->getPathname()), $output, $return);
            if (0 !== $return) {
                $this->output($output, true, $this->halt);
                $ret = false;
                if ($this->halt) {
                    return $ret;
                }
            }
        }

        if ($ret) {
            $this->output("\t\t\tOK");
        }

        return true;
    }
}
