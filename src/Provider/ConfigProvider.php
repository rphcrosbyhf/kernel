<?php declare(strict_types = 1);

namespace Phlux\Kernel\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Registeres objects related to configuration
 */
final class ConfigProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        'config'
    ];

    /**
     * @var string
     */
    private $dir;

    /**
     * @param string $dir
     */
    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->getContainer()->add('config', include sprintf('%s/%s', $this->dir, 'app.php'));
    }
}