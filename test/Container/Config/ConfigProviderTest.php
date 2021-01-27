<?php

namespace AntidotTest\React\DBAL\Container\Config;

use Antidot\React\DBAL\Container\Config\ConfigProvider;
use Antidot\React\DBAL\Container\ConnectionFactory;
use Drift\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testItShouldReturnTheConfigArray(): void
    {
        $configProvider = new ConfigProvider();
        $this->assertSame([
            'dependencies' => [
                'factories' => [
                    Connection::class => ConnectionFactory::class,
                ],
            ],
        ], $configProvider());
    }
}
