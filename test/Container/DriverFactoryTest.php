<?php

declare(strict_types=1);

namespace AntidotTest\React\DBAL\Container;

use Antidot\React\DBAL\Container\DriverFactory;
use Drift\DBAL\Driver\Mysql\MysqlDriver;
use Drift\DBAL\Driver\PostgreSQL\PostgreSQLDriver;
use Drift\DBAL\Driver\SQLite\SQLiteDriver;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;

class DriverFactoryTest extends TestCase
{
    /** @dataProvider getValidConfigs */
    public function testItShouldCreateInstancesOfSQLDriver(array $config, $expectedInstance): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['config'], [LoopInterface::class])
            ->willReturnOnConsecutiveCalls($config, $this->createMock(LoopInterface::class));
        $factory = new DriverFactory();
        $driver = $factory->__invoke($container);
        $this->assertInstanceOf($expectedInstance, $driver);
    }

    public function testItShouldThrowExceptionWhenCantFindValidDriver(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'dbal' => [
                    'connections' => [
                        'default' => [
                            'driver' => 'pdo',
                        ],
                    ],
                ],
            ]);
        $factory = new DriverFactory();
        $factory->__invoke($container);
    }

    public function getValidConfigs()
    {
        return [
            'MySQL Driver' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'mysql',
                            ],
                        ],
                    ],
                ],
                MysqlDriver::class,
            ],
            'PostgresSQL Driver' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'postgres',
                            ],
                        ],
                    ],
                ],
                PostgreSQLDriver::class,
            ],
            'SQLite Driver' => [
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'sqlite',
                            ],
                        ],
                    ],
                ],
                SQLiteDriver::class,
            ],
        ];
    }
}
