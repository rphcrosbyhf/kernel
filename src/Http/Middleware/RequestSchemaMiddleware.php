<?php declare(strict_types = 1);

namespace Phlux\Kernel\Http\Middleware;

use League\JsonGuard\Validator;
use League\JsonReference\Dereferencer;
use League\JsonReference\Loader\FileLoader;
use Phlux\Kernel\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Middleware for validating request schema JSON
 */
final class RequestSchemaMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $schema = (new FileLoader())->load($this->path);
        $schema = (new Dereferencer())->dereference($schema);
        $validator = new Validator($request->getParsedBody(), $schema);

        if ($validator->fails()) {
            $response->getBody()->write(json_encode([
                'status_code' => 400,
                'reason_phrase' => 'The request does not match the expected schema',
                'errors' => $validator->errors()
            ]));

            return $response->withStatus(400);
        }

        return $next($request, $response);
    }
}