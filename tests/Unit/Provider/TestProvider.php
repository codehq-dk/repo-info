<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Provider;

use CodeHqDk\RepositoryInformation\ExamplePlugin\InformationBlocks\HelloWorldInformationBlock;
use CodeHqDk\RepositoryInformation\ExamplePlugin\Provider\HelloWorldInformationFactoryProvider;
use CodeHqDk\RepositoryInformation\Factory\GitInformationFactory;
use CodeHqDk\RepositoryInformation\InformationBlocks\GitNameInformationBlock;
use CodeHqDk\RepositoryInformation\Model\GitRepository;
use CodeHqDk\RepositoryInformation\Model\Repository;
use CodeHqDk\RepositoryInformation\Model\RepositoryCharacteristics;
use CodeHqDk\RepositoryInformation\RepositoryInformationProvider;
use CodeHqDk\RepositoryInformation\Service\InformationBlockFilterService;
use CodeHqDk\RepositoryInformation\Services\InformationBlockService;
use CodeHqDk\RepositoryInformation\Services\SimpleInformationBlockFilterService;
use CodeHqDk\RepositoryInformation\Tests\Unit\Mock\MockInformationFactory1;
use Slince\Di\Container;

class TestProvider
{
    public const TEST_FILTER_ONLY_HELLO_WORLD = '8f3251c1-d998-41d6-a45f-1e56513191ed';
    public const TEST_FILTER_HELLO_WORLD_AND_GIT_NAME = '7f3251c1-d998-41d6-c45f-1e56513191ed';

    private Container $dependency_injection_container;

    /**
     * @param Repository[] $repository_list
     */
    public function __construct(private readonly array $repository_list =
        [
            new GitRepository(
                'https://github.com/codehq-dk/repo-info-contracts.git',
                new RepositoryCharacteristics(false, true, true, false),
            ),
            new GitRepository(
                'https://github.com/codehq-dk/repo-info-example-plugin.git',
                new RepositoryCharacteristics(true, true, true, false)
            ),
            new GitRepository(
                'https://github.com/codehq-dk/linux-bash-helper.git',
                new RepositoryCharacteristics(true, true, false, false)
            ),
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
                self::TEST_FILTER_ONLY_HELLO_WORLD => [
                    HelloWorldInformationBlock::class,
                ],
                self::TEST_FILTER_HELLO_WORLD_AND_GIT_NAME => [
                    HelloWorldInformationBlock::class,
                    GitNameInformationBlock::class
                ]
            ]
        ]);

        $this->dependency_injection_container->setAlias(InformationBlockFilterService::class, SimpleInformationBlockFilterService::class);

        $repository_information_provider = new RepositoryInformationProvider(
            dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . "data",
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
