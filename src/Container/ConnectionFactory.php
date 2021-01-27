<?php

declare(strict_types=1);

namespace Antidot\React\DBAL\Container;

use Antidot\React\DBAL\Container\Config\ConfigProvider;
use Drift\DBAL\Connection;
use Drift\DBAL\Credentials;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

class ConnectionFactory
{
    private const VALID_STRINGS= [
        'host',
        'port',
        'user',
        'password',
        'dbname',
    ];

    public function __invoke(
        ContainerInterface $container,
        string $connectionName = ConfigProvider::DEFAULT_CONNECTION
    ): Connection {
        /** @var array<string, array<string, array>> $globalConfig */
        $globalConfig = $container->get('config');
        /** @var array<string, array> $dbalConfig */
        $dbalConfig = $globalConfig['dbal']['connections'];
        /** @var array<string, mixed> $config */
        $config = $dbalConfig[$connectionName];
        /** @var DriverFactory $driverFactory */
        $driverFactory = $container->get(DriverFactory::class);
        /** @var PlatformFactory $platformFactory */
        $platformFactory = $container->get(PlatformFactory::class);
        $this->assertConfig($config);

        /** @psalm-suppress MixedArgument */
        return Connection::create(
            $driverFactory($container),
            new Credentials(
                $config['host'],
                $config['port'],
                $config['user'],
                $config['password'],
                $config['dbname'],
                $config['options']
            ),
            $platformFactory($container)
        );
    }

    private function assertConfig(array $config): void
    {
        foreach (self::VALID_STRINGS as $index) {
            if (false === array_key_exists($index, $config) || false === is_string($config[$index])) {
                throw new InvalidArgumentException(sprintf(
                    'The connection config key "%s" is required and must be a string.',
                    $index
                ));
            }
        }

        if (false === array_key_exists('options', $config)) {
            return;
        }

        if (false === is_array($config['options'])) {
            throw new InvalidArgumentException(
                'The "options" connection key must be an array.'
            );
        }
    }
}
