<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Http\Server;

use Psr\Http\Message\ServerRequestInterface;
use Windwalker\Event\EventListenableTrait;
use Windwalker\Http\Event\WebRequestEvent;
use Windwalker\Http\HttpFactory;
use Windwalker\Http\Output\OutputInterface;
use Windwalker\Http\Output\StreamOutput;

/**
 * The WebAdapter class.
 */
class PhpAdapter implements ServerAdapterInterface
{
    use EventListenableTrait;

    protected ?OutputInterface $output = null;

    protected HttpFactory $httpFactory;

    /**
     * WebAdapter constructor.
     *
     * @param  HttpFactory|null      $httpFactory
     * @param  OutputInterface|null  $output
     */
    public function __construct(?HttpFactory $httpFactory = null, ?OutputInterface $output = null)
    {
        $this->output      = $output ?? $this->getOutput();
        $this->httpFactory = $httpFactory ?? new HttpFactory();
    }

    public function resume(): void
    {
        $this->handle();
    }

    public function handle(?ServerRequestInterface $request = null): void
    {
        /** @var WebRequestEvent $event */
        $event = $this->emit(
            WebRequestEvent::wrap(
                'request',
                [
                    'request' => $request ?? $this->httpFactory->createServerRequestFromGlobals(),
                    'response' => $this->httpFactory->createResponse()
                ]
            )
        );

        $this->getOutput()->respond($event->getResponse());
    }

    public function pause(): void
    {
        //
    }

    public function close(): void
    {
        //
    }

    /**
     * Method to get property Output
     *
     * @return  OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output ??= new StreamOutput();
    }

    /**
     * Method to set property output
     *
     * @param   OutputInterface $output
     *
     * @return  static  Return self to support chaining.
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }
}
