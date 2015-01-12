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
 * Rebase processor.
 *
 * @package Monohook
 * @subpackage Monohook.Processor
 * @author Jad Bitar <jadbitar@mac.com>
 */
class RebaseProcessor extends AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ProviderInterface $Provider)
    {
        if (!$Provider->isShallow()) {
            $method = strtolower($Provider->getName());
            if (method_exists($this, $method)) {
                return $this->$method($Provider);
            }
        }

        return true;
    }

    protected function git($Provider)
    {
        if ($Provider->getHook() != 'pre-rebase') {
            return true;
        }

        $argv = $_SERVER['argv'];
        $rebaseBranch = $argv[0];
        if (!empty($argv[1])) {
            $topicBranch = 'refs/heads/' . $argv[1];
        } else {
            $topicBranch = chop($Provider->execute('git symbolic-ref HEAD'));
        }

        $shas = explode("\n", $Provider->execute("git rev-list $rebaseBranch..$topicBranch"));
        $refs = array_map('trim', explode("\n", $Provider->execute("git branch -r")));

        foreach ($shas as $sha) {
            foreach ($refs as $ref) {
                $pushed = chop($Provider->execute("git rev-list ^$sha^@ refs/remotes/$ref")) == $sha;
                if ($pushed) {
                    $this->output("Commit $sha has already been pushed to $ref", true, true);
                    return;
                }
            }
        }

        $message = "Rebase of $rebaseBranch does not overwrite any of $topicBranch remote commits";
        $this->output($message);
        return true;
    }

    protected function hg($Provider)
    {
        throw new \Monohook\Exception\MissingMethodException(__METHOD__);
    }

    protected function svn($Provider)
    {
        throw new \Monohook\Exception\MissingMethodException(__METHOD__);
    }
}
