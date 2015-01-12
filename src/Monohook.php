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

use Monohook\Processor\AbstractProcessor;
use Monohook\Provider\ProviderInterface;

/**
 * Monohook.
 *
 * @package Monohook
 * @author Jad Bitar <jadbitar@mac.com>
 */
class Monohook
{
    /**
     * Provider - version control system wrapper.
     *
     * @var Monohook\Provider\ProviderInterface
     */
    public $Provider;

    /**
     * Processors stack.
     *
     * @var array
     */
    protected $processors;

    /**
     * Constructor.
     *
     * @param Provider $Provider
     * @param array $config
     */
    public function __construct(ProviderInterface $Provider, array $config)
    {
        $this->Provider = $Provider;
        $this->config($config);
    }

    /**
     * Configures hook.
     *
     * @param array $config
     * @return void
     */
    public function config($config = array())
    {
        if (empty($this->processors)) {
            $this->processors = array();
        }

        if (empty($config)) {
            return;
        }

        $defaults = array();
        if (!empty($config[0])) {
            if (empty($config['*'])) {
                $config['*'] = $config[0];
            }
            unset($config[0]);
        }

        if (!empty($config['*'])) {
            $defaults = $config['*'];
        }

        $processors = current(array_merge(array($this->Provider->getHook() => $defaults), $config));

        foreach ($processors as $processor) {
            $this->pushProcessor($processor);
        }
    }

    /**
     * Gets next processor in stack.
     *
     * @return mixed
     */
    public function popProcessor()
    {
        if (empty($this->processors)) {
            throw new \LogicException('You tried to pop from an empty hook stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * Add processor to stack.
     *
     * @param mixed $callback
     * @return void
     */
    public function pushProcessor($callback)
    {
        if (!is_callable($callback)) {
            throw new  \InvalidArgumentException(
                'Hooks must be valid callables (callback or object with an __invoke method), '
                . var_export($callback, true)
                . ' given.'
            );
        }

        array_push($this->processors, $callback);
    }

    /**
     * Runs all attached processors.
     *
     * @return void
     */
    public function run()
    {
        $result = AbstractProcessor::PASS;
        $this->Provider->stash();
        foreach ((array) $this->processors as $processor) {
            if (!call_user_func($processor, $this->Provider)) {
                $result = AbstractProcessor::FAIL;
            }
        }
        $this->Provider->unstash();
        return $result;
    }
}
