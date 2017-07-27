<?php declare(strict_types = 1);

namespace Phlux\Kernel\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\RouteCollection;
use League\Route\Strategy\JsonStrategy;

/**
 * Registers objects related to HTTP routes
 */
final class RouteProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        RouteCollection::class,
    ];

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
    public function register()
    {
        $this->getContainer()->add(RouteCollection::class, function () {
            $router = new RouteCollection($this->getContainer());
            $router->setStrategy(new JsonStrategy());
            include $this->path;
            return $router;
        });
    }
}