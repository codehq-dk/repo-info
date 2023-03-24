<?php

namespace CodeHqDk\RepositoryInformation\Tests;

use CodeHqDk\RepositoryInformation\InformationBlocks\HelloWorldInformationBlock;
use CodeHqDk\RepositoryInformation\InformationBlocks\RepositoryNameInformationBlock;
use CodeHqDk\RepositoryInformation\Model\GitRepository;
use CodeHqDk\RepositoryInformation\Model\Repository;
use CodeHqDk\RepositoryInformation\Model\RepositoryCharacteristics;
use CodeHqDk\RepositoryInformation\Provider\HelloWorldInformationFactoryProvider;
use CodeHqDk\RepositoryInformation\RepositoryInformationProvider;
use CodeHqDk\RepositoryInformation\Service\InformationBlockFilterService;
use CodeHqDk\RepositoryInformation\Services\InformationBlockService;
use CodeHqDk\RepositoryInformation\Services\SimpleInformationBlockFilterService;
use Slince\Di\Container;

class TestProvider
{
    private Container $dependency_injection_container;

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
        $this->dependency_injection_container = new Container();
        $this->dependency_injection_container->setDefaults(
            [
                'share' => true,
                'autowire' => true,
                'autoregister' => true
            ]
        );

        $this->dependency_injection_container->register(SimpleInformationBlockFilterService::class)->setArguments([
            'information_block_service' => $this->dependency_injection_container->get(InformationBlockService::class),
            'uuid_to_information_block_class_name_list_map' => [
                '8f3251c1-d998-41d6-a45f-1e56513191ed' => [
                    HelloWorldInformationBlock::class,
                    RepositoryNameInformationBlock::class
                ]
            ]
        ]);

        $this->dependency_injection_container->setAlias(InformationBlockFilterService::class, SimpleInformationBlockFilterService::class);

        $repository_information_provider = new RepositoryInformationProvider(
            __DIR__ . DIRECTORY_SEPARATOR . "data",
            $this->repository_list,
            $this->dependency_injection_container
        );

        $repository_information_provider->registerDependencies();
        $repository_information_provider->addInformationFactoryProvider(new HelloWorldInformationFactoryProvider());
    }

    public function getDependencyInjectionContainer(): Container
    {
        return $this->dependency_injection_container;
    }
}