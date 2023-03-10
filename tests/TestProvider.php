<?php

namespace CodeHqDk\RepositoryInformation\Tests;

use CodeHqDk\RepositoryInformation\Model\GitRepository;
use CodeHqDk\RepositoryInformation\Model\Repository;
use CodeHqDk\RepositoryInformation\Model\RepositoryCharacteristics;
use CodeHqDk\RepositoryInformation\Provider\HelloWorldInformationFactoryProvider;
use CodeHqDk\RepositoryInformation\RepositoryInformationProvider;
use Slince\Di\Container;

class TestProvider
{
    private Container $dependecy_injection_container;

    /**
     * @param Repository[] $repository_list
     */
    public function __construct(private readonly array $repository_list =
        [
            new GitRepository(
                'repo-info-contracts',
                'Test repository 1',
                'https://github.com/codehq-dk/repo-info-contracts.git',
                new RepositoryCharacteristics(true, true, true, false),
            ),
            new GitRepository(
                'repo-info-example-plugin',
                'Test repository 2',
                'https://github.com/codehq-dk/repo-info-example-plugin.git',
                new RepositoryCharacteristics(true, true, true, false)
            ),
            new GitRepository(
                'code-hq-webpage',
                'Test repository 3',
                'git@github.com:codehq-dk/webpage.git',
                new RepositoryCharacteristics(true, false, false, false)
            )
        ]
    ) {
    }

    public function initialize(): void
    {
        $this->dependecy_injection_container = new Container();
        $this->dependecy_injection_container->setDefaults(
            [
                'share' => true,
                'autowire' => false,
                'autoregister' => true
            ]
        );

        $repository_information_provider = new RepositoryInformationProvider(
            __DIR__ . DIRECTORY_SEPARATOR . "data",
            $this->repository_list,
            $this->dependecy_injection_container
        );

        $repository_information_provider->registerDependencies();
        $repository_information_provider->addInformationFactoryProvider(new HelloWorldInformationFactoryProvider());
    }

    public function getDependencyInjectionContainer(): Container
    {
        return $this->dependecy_injection_container;
    }
}