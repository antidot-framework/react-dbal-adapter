<?php

declare(strict_types=1);

namespace Antidot\React\DBAL\Container;

use Antidot\React\DBAL\Container\Config\ConfigProvider;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Psr\Container\ContainerInterface;

class PlatformFactory extends DriverInConfig
{
    public function __invoke(
        ContainerInterface $container,
        string $connectionName = ConfigProvider::DEFAULT_CONNECTION
    ): AbstractPlatform {
        /** @var array<string, array<string, array>> $globalConfig */
        $globalConfig = $container->get('config');

        /** @var array<string, array> $dbalConfig */
        $dbalConfig = $globalConfig['dbal']['connections'];

        /** @var array<string, string> $config */
        $config = $dbalConfig[$connectionName];
        $driverName = $this->getDriverFromConfig($config);

        if ('mysql' === $driverName) {
            return new MySqlPlatform();
        }

        if ('postgres' === $driverName) {
            if (false === empty($config['version']) && '9.4' === $config['version']) {
                return new PostgreSQL94Platform();
            }

            return new PostgreSQL100Platform();
        }

        return new SqlitePlatform();
    }
}
