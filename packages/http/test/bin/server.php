<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

$autoload = __DIR__ . '/../../vendor/autoload.php';

if (!is_file($autoload)) {
    $autoload = __DIR__ . '/../../../../vendor/autoload.php';
}

include $autoload;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Windwalker\Http\Request\ServerRequestFactory;
use Windwalker\Http\WebHttpServer;
use Windwalker\Stream\StringStream;

$server = new WebHttpServer(
    function (Request $req, Response $res) {
        $app = new class
        {
            public ServerRequestInterface $req;
            public Response $res;

            public function __invoke(ServerRequestInterface $req, Response $res)
            {
                $uri = $req->getUri();
                $path = trim($uri->getPath(), '/') ?: 'index';

                $this->req = $req;
                $this->res = $res;

                if (method_exists($this, $path)) {
                    return $this->$path();
                }

                return $res->withStatus(404);
            }

            public function index(): Response
            {
                $headers = $this->req->getHeaders();
                $head = '';
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
                $uri = $this->req->getUri();
                $query = $uri->getQueryValues();

                return $this->response(json_encode($query));
            }

            public function server()
            {
                return $this->response(json_encode($this->req->getServerParams()));
            }

            protected function response($value): Response
            {
                return $this->res->withBody(
                    new StringStream($value)
                );
            }
        };

        return $app($req, $res);
    },
    ServerRequestFactory::createFromGlobals(),
);
$server->listen();
