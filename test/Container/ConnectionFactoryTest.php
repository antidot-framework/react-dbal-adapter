<?php

declare(strict_types=1);

namespace AntidotTest\React\DBAL\Container;

use Antidot\React\DBAL\Container\ConnectionFactory;
use Antidot\React\DBAL\Container\DriverFactory;
use Antidot\React\DBAL\Container\PlatformFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ConnectionFactoryTest extends TestCase
{
    public function testItShouldCreateInstancesOfDBALConnection(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(['config'], [DriverFactory::class], [PlatformFactory::class])
            ->willReturnOnConsecutiveCalls(
                [
                    'dbal' => [
                        'connections' => [
                            'default' => [
                                'driver' => 'sqlite',
                                'host' => '0.0.0.0',
                                'port' => '3306',
                                'user' => 'someUser',
                                'password' => 'superSecret',
                                'dbname' => 'some_database_name',
                                'options' => [],
                            ],
                        ],
                    ],
                ],
                $this->createMock(DriverFactory::class),
                $this->createMock(PlatformFactory::class)
            );
        $factory = new ConnectionFactory();

        $factory->__invoke($container);
    }

    /** @dataProvider getInvalidConfigs */
    public function testItShouldThrowExceptionWithInvalidConfig(array $config): void
    {
        $this->expectException(InvalidArgumentException::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(['config'], [DriverFactory::class], [PlatformFactory::class])
            ->willReturnOnConsecutiveCalls(
                [
                    'dbal' => [
                        'connections' => [
                            'default' => $config,
                        ],
                    ],
                ],
                $this->createMock(DriverFactory::class),
                $this->createMock(PlatformFactory::class)
            );
        $factory = new ConnectionFactory();

        $factory->__invoke($container);
    }

    public function getInvalidConfigs(): array
    {
        return [
            [
                [
                    'port' => '3306',
                    'user' => 'someUser',
                    'password' => 'superSecret',
                    'dbname' => 'some_database_name',
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'user' => 'someUser',
                    'password' => 'superSecret',
                    'dbname' => 'some_database_name',
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'password' => 'superSecret',
                    'dbname' => 'some_database_name',
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'user' => 'someUser',
                    'dbname' => 'some_database_name',
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'user' => 'someUser',
                    'password' => 'superSecret',
                ]
            ],
            [
                [
                    'host' => [],
                    'port' => '3306',
                    'user' => 'someUser',
                    'password' => 'superSecret',
                    'dbname' => 'some_database_name',
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => [],
                    'user' => 'someUser',
                    'password' => 'superSecret',
                    'dbname' => 'some_database_name',
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'user' => [],
                    'password' => 'superSecret',
                    'dbname' => 'some_database_name',
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'user' => 'someUser',
                    'password' => null,
                    'dbname' => 'some_database_name',
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'user' => 'someUser',
                    'password' => 'superSecret',
                    'dbname' => [],
                    'options' => [],
                ]
            ],
            [
                [
                    'host' => '0.0.0.0',
                    'port' => '3306',
                    'user' => 'someUser',
                    'password' => 'superSecret',
                    'dbname' => 'some_database_name',
                    'options' => 'fail',
                ]
            ],
        ];
    }
}
