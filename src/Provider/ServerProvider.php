<?php declare(strict_types = 1);

namespace Phlux\Kernel\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\RouteCollection;
use MKraemer\ReactPCNTL\PCNTL;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use React\Http\Response;
use React\Http\Server;
use React\Promise\PromiseInterface;
use React\Socket\Server as SocketServer;
use function React\Promise\Stream\buffer;
use function RingCentral\Psr7\stream_for;

/**
 * Registers objects related to the HTTP server
 */
final class ServerProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * @var array
     */
    protected $provides = [
        LoopInterface::class,
        SocketServer::class,
        Server::class,
    ];

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $address;

    /**
     * @param string $address
     * @param int $port
     */
    public function __construct(string $address, int $port)
    {
        $this->port = $port;
        $this->address = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerSocketServer();
        $this->registerHttpServer();
    }

    /**
     * @return void
     */
    private function registerSocketServer() : void
    {
        $this->getContainer()->add(
            SocketServer::class,
            new SocketServer(
                sprintf('tcp://%s:%d', $this->address, $this->port),
                $this->getContainer()->get(LoopInterface::class)
            )
        );
    }

    /**
     * @return void
     */
    private function registerHttpServer() : void
    {
        $this->getContainer()->add(
            Server::class,
            new Server(function (ServerRequestInterface $request) : PromiseInterface {
                return buffer($request->getBody())->then(function (string $body) use ($request) : ResponseInterface {
                    return $this->getContainer()
                        ->get(RouteCollection::class)
                        ->dispatch($request->withBody(stream_for($body)), new Response());
                });
            })
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $loop = new StreamSelectLoop();
        $pcntl = new PCNTL($loop);

        $pcntl->on(SIGINT, function () {
            echo 'Shutting down...' . PHP_EOL;
            die;
        });

        $this->getContainer()->add(PCNTL::class, $pcntl);
        $this->getContainer()->add(LoopInterface::class, $loop);
    }
}