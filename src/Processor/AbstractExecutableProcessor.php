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

/**
 * Base ExecutableProcessor class to structure other processors.
 *
 * @package Monohook
 * @subpackage Monohook.Processor
 * @author Jad Bitar <jadbitar@mac.com>
 */
abstract class AbstractExecutableProcessor extends AbstractProcessor
{
    /**
     * Command.
     *
     * @var string
     */
    protected $executable;

    /**
     * Set executable command.
     *
     * @param string $executable Command.
     */
    final public function setExecutable($executable)
    {
        $this->executable = $executable;
    }

    /**
     * Executes the command.
     *
     * @param string $cmd Command to execute.
     * @param array $output Returned output.
     * @param int $return Command exit status.
     * @return [type]
     */
    final public function execute($cmd, &$output, &$return)
    {
        exec("$this->executable $cmd", $output, $return);
    }
}
