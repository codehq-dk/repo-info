<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Registries;

use CodeHqDk\RepositoryInformation\Factory\GitInformationFactory;
use CodeHqDk\RepositoryInformation\Registry\InformationFactoryRegistry;
use PHPUnit\Framework\TestCase;

class InformationFactoryRegistryTest extends TestCase
{
    public function testMethods(): void
    {
        $registry = new InformationFactoryRegistry();

        $factory = new GitInformationFactory();

        $registry->addFactory($factory);

        $expected = [$factory];

        $actual = $registry->listFactories();

        $this->assertEquals($actual, $expected);

        $registry->setFactories([$factory, $factory]);

        $expected = [$factory, $factory];

        $actual = $registry->listFactories();
        $this->assertEquals($actual, $expected);
    }
}
