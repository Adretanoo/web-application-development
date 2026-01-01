<?php

namespace Acer\Pr8\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (!isset($_SESSION['user_id'])) {
            throw new \RuntimeException('Unauthorized');
        }

        return $handler->handle($request);
    }
}
