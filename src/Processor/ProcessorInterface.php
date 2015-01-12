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
 * Processor interface.
 *
 * @package Monohook
 * @subpackage Monohook.Processor
 * @author Jad Bitar <jadbitar@mac.com>
 */
interface ProcessorInterface
{
    /**
     * Invoker.
     *
     * @param ProviderInterface $Provider [description]
     * @return [type]
     */
    public function __invoke(ProviderInterface $Provider);
}
