<?php

declare(strict_types=1);

namespace AntidotTest\React\DBAL\Container;

use Antidot\React\DBAL\Container\PlatformFactory;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class PlatformFactoryTest extends TestCase
{
    /** @dataProvider getValidConfigs */
    public function testItShouldCreatePlatformInstances(array $config, string $expectedInstance): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new PlatformFactory();

        $platform = $factory->__invoke($container);
        $this->assertInstanceOf($expectedInstance, $platform);
    }

    public function getValidConfigs()
    {
        return [
            'MySQL Platform' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'mysql',
                            ],
                        ],
                    ],
                ],
                MySqlPlatform::class,
            ],
            'PostgresSQL Default Platform' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'postgres',
                            ],
                        ],
                    ],
                ],
                PostgreSQL100Platform::class,
            ],
            'PostgresSQL 10 Platform' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'postgres',
                                'version' => '10',
                            ],
                        ],
                    ],
                ],
                PostgreSQL100Platform::class,
            ],
            'PostgresSQL 9.4 Platform' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'postgres',
                                'version' => '9.4',
                            ],
                        ],
                    ],
                ],
                PostgreSQL94Platform::class,
            ],
            'SQLite Platform' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'sqlite',
                            ],
                        ],
                    ],
                ],
                SqlitePlatform::class,
            ],
        ];
    }
}
