<?php declare(strict_types = 1);

namespace Phlux\Kernel\Http\Middleware;

use Phlux\Kernel\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Middleware for logging requests as they're recieved
 */
final class RequestLoggingMiddleware implements MiddlewareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $this->logger->critical('Received request', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
        ]);

        $next($request, $response);
    }
}