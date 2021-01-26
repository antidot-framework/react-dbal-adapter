<?php

namespace Antidot\React\DBAL\Container\Config;

class ConfigProvider
{
    public const DEFAULT_CONNECTION = 'default';

    public function __invoke(): array
    {
        return [];
    }
}
