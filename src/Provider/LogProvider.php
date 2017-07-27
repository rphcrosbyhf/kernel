<?php declare(strict_types = 1);

namespace Phlux\Kernel\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Registers objects related to logging
 */
final class LogProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        LoggerInterface::class,
    ];

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path)
    {
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->getContainer()->add(LoggerInterface::class, function () {
            return new Logger($this->name, [new StreamHandler($this->path)]);
        });
    }
}