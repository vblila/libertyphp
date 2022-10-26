<?php declare(strict_types=1);

namespace Libertyphp\Tests\Router;

use GuzzleHttp\Psr7\Response;
use Libertyphp\Http\ActionController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SimpleActionController extends ActionController
{
    public function execute(array $routeParams, ServerRequestInterface $request): ResponseInterface
    {
        $responseBody = 'Simple response';

        $requestBody = $request->getBody()->getContents();
        if ($requestBody) {
            $responseBody .= ' with request body ' . $requestBody;
        }

        if ($routeParams) {
            $responseBody .= ' with route params';
            foreach ($routeParams as $param => $value) {
                $responseBody .= ' ' . $param . '=' . $value;
            }
        }

        return new Response(200, [], $responseBody);
    }
}
