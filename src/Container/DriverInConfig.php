<?php

declare(strict_types=1);

namespace Antidot\React\DBAL\Container;

use InvalidArgumentException;

abstract class DriverInConfig
{
    private const VALID_DRIVERS = [
        'mysql',
        'postgres',
        'sqlite',
    ];

    /**
     * @param array<string, string> $config
     * @return string
     */
    protected function getDriverFromConfig(array $config): string
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
                implode(',', self::VALID_DRIVERS)
            ));
        }

        return $driverName;
    }
}
