<?php

declare(strict_types=1);

namespace Antidot\React\DBAL\Container;

use Drift\DBAL\Driver\Driver;
use Drift\DBAL\Driver\Mysql\MysqlDriver;
use Drift\DBAL\Driver\PostgreSQL\PostgreSQLDriver;
use Drift\DBAL\Driver\SQLite\SQLiteDriver;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;

class DriverFactory
{
    private const DEFAULT_CONNECTION = 'default';
    private const VALID_DRIVERS = [
        'mysql',
        'postgre',
        'sqlite',
    ];

    public function __invoke(ContainerInterface $container, string $connectionName = self::DEFAULT_CONNECTION): Driver
    {
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

        if ('postgre' === $driverName) {
            return new PostgreSQLDriver($loop);
        }

        return new SQLiteDriver($loop);
    }

    /**
     * @param array<string, string> $config
     * @return string
     */
    private function getDriverFromConfig(array $config): string
    {
        $driverName = '';

        if (array_key_exists('driver', $config)) {
            $driverName = $config['driver'];
        }

        if (array_key_exists('dsn', $config)) {
            // TODO get driver from dsn.
            $driverName = $config['dsn'];
        }

        if (false === isset($driverName)
            || false === in_array($driverName, self::VALID_DRIVERS)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid driver name give, it must be one of the following drivers: %s ',
                implode(self::VALID_DRIVERS)
            ));
        }

        return $driverName;
    }
}
