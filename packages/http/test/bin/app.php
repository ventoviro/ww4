<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Windwalker\Http\HttpFactory;
use Windwalker\Stream\StringStream;

return new class {
    public ServerRequestInterface $req;

    public function __invoke(ServerRequestInterface $req)
    {
        $uri  = $req->getUri();

        echo "Request from: $uri\n";

        $path = trim($uri->getPath(), '/') ?: 'index';

        $this->req = $req;

        if (method_exists($this, $path)) {
            return $this->$path();
        }

        return (new HttpFactory())->createResponse()->withStatus(404);
    }

    public function index(): ResponseInterface
    {
        $headers = $this->req->getHeaders();
        $head    = '';
        foreach ($headers as $name => $headerItems) {
            $head .= sprintf("%s: %s\n", $name, $this->req->getHeaderLine($name));
        }

        $fp = fopen('php://input', 'r');

        $body = stream_get_contents($fp);

        fclose($fp);

        if (!$body) {
            $body = http_build_query($_POST);
        }

        $output = <<<BODY
                {$this->req->getMethod()} {$this->req->getUri()}
                {$head}
                {$body}
                BODY;

        return $this->response($output);
    }

    public function json()
    {
        $uri   = $this->req->getUri();
        $query = $uri->getQueryValues();

        return $this->response(json_encode($query));
    }

    public function server()
    {
        return $this->response(json_encode($this->req->getServerParams()));
    }

    protected function response($value): ResponseInterface
    {
        return (new HttpFactory())
            ->createResponse()
            ->withBody(
                new StringStream($value)
            );
    }
};
