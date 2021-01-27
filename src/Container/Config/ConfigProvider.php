<?php

namespace Antidot\React\DBAL\Container\Config;

use Antidot\React\DBAL\Container\ConnectionFactory;
use Drift\DBAL\Connection;

class ConfigProvider
{
    public const DEFAULT_CONNECTION = 'default';

    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    Connection::class => ConnectionFactory::class,
                ],
            ],
        ];
    }
}
