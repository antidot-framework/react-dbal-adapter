<?php

declare(strict_types=1);

namespace Antidot\React\DBAL\Container;

use Antidot\React\DBAL\Container\Config\ConfigProvider;
use Drift\DBAL\Driver\Driver;
use Drift\DBAL\Driver\Mysql\MysqlDriver;
use Drift\DBAL\Driver\PostgreSQL\PostgreSQLDriver;
use Drift\DBAL\Driver\SQLite\SQLiteDriver;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;

class DriverFactory extends DriverInConfig
{

    public function __invoke(
        ContainerInterface $container,
        string $connectionName = ConfigProvider::DEFAULT_CONNECTION
    ): Driver {
        /** @var array<string, array<string, array>> $globalConfig */
        $globalConfig = $container->get('config');

        /** @var array<string, array> $dbalConfig */
        $dbalConfig = $globalConfig['dbal']['connections'];

        /** @var array<string, string> $config */
        $config = $dbalConfig[$connectionName];
        $driverName = $this->getDriverFromConfig($config);

        /** @var LoopInterface $loop */
        $loop = $container->get(LoopInterface::class);

        if ('mysql' === $driverName) {
            return new MysqlDriver($loop);
        }

        if ('postgres' === $driverName) {
            return new PostgreSQLDriver($loop);
        }

        return new SQLiteDriver($loop);
    }
}
