<?php declare(strict_types = 1);

namespace Phlux\Kernel\Http\Middleware;

use League\Route\Http\Exception\BadRequestException;
use Phlux\Kernel\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Middleware for decoding JSON requests
 */
final class JsonDecodingMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (!in_array('application/json', $request->getHeader('Content-Type'))) {
            return $next($request, $response);
        }

        $decoded = json_decode((string) $request->getBody());

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestException();
        }

        return $next($request->withParsedBody($decoded), $response);
    }
}