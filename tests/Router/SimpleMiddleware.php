<?php declare(strict_types=1);

namespace Libertyphp\Tests\Router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SimpleMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('X-Auth-Token')) {
            return new Response(307, ['Location' => '/login']);
        }

        return $handler->handle($request);
    }
}
