<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Services;

use CodeHqDk\RepositoryInformation\ExamplePlugin\InformationBlocks\HelloWorldInformationBlock;
use CodeHqDk\RepositoryInformation\InformationBlocks\ErrorBlock;
use CodeHqDk\RepositoryInformation\InformationBlocks\GitNameInformationBlock;
use CodeHqDk\RepositoryInformation\InformationBlocks\RequirementsNotMetBlock;
use CodeHqDk\RepositoryInformation\Model\GitRepository;
use CodeHqDk\RepositoryInformation\Model\RepositoryCharacteristics;
use CodeHqDk\RepositoryInformation\Model\RepositoryInformation;
use CodeHqDk\RepositoryInformation\Registry\InformationFactoryRegistry;
use CodeHqDk\RepositoryInformation\Services\RepositoryInformationService;
use CodeHqDk\RepositoryInformation\Tests\Unit\Mock\MockInformationFactory1;
use CodeHqDk\RepositoryInformation\Tests\Unit\Mock\MockInformationFactory2;
use CodeHqDk\RepositoryInformation\Tests\Unit\Provider\TestProvider;
use CodeHqDk\RepositoryInformation\Tests\Unit\TestHelpers\TestHelper;
use PHPUnit\Framework\TestCase;

class RepositoryInformationServiceTest extends TestCase
{
    private object $dependency_injection_container;

    protected function setUp(): void
    {
        $data_folder = dirname(__FILE__, 3) . '/data';
        TestHelper::deleteFolder($data_folder);
    }

    public function testListCanReturnRequirementsNotMetBlock(): void
    {

        $test_provider = new TestProvider(
            [
                new GitRepository(
                    'https://github.com/codehq-dk/repo-info-contracts.git',
                    new RepositoryCharacteristics(true, true, true, false),
                )
            ]
        );
        $test_provider->initialize();
        $this->dependency_injection_container = $test_provider->getDependencyInjectionContainer();
        $this->dependency_injection_container->get(InformationFactoryRegistry::class)->addFactory(new MockInformationFactory1());

        /**
         * @var RepositoryInformationService $repo_info_service
         */
        $repo_info_service = $this->dependency_injection_container->get(RepositoryInformationService::class);

        $repo_info_list = $repo_info_service->list();
        $this->assertCount(1, $repo_info_list);
        $repository_information = $repo_info_list[0];
        $this->assertInstanceOf(RepositoryInformation::class, $repository_information);
        $blocks = $repository_information->listInformationBlocks();
        $this->assertCount(3, $blocks);
        $this->assertInstanceOf(GitNameInformationBlock::class, $repository_information->listInformationBlocks()[0]);
        $this->assertInstanceOf(HelloWorldInformationBlock::class, $repository_information->listInformationBlocks()[1]);
        $this->assertInstanceOf(RequirementsNotMetBlock::class, $repository_information->listInformationBlocks()[2]);
    }

    public function testListCanReturnErrorBlock(): void
    {

        $test_provider = new TestProvider(
            [
                new GitRepository(
                    'https://github.com/codehq-dk/repo-info-contracts.git',
                    new RepositoryCharacteristics(true, true, true, false),
                )
            ]
        );
        $test_provider->initialize();
        $this->dependency_injection_container = $test_provider->getDependencyInjectionContainer();
        $this->dependency_injection_container->get(InformationFactoryRegistry::class)->addFactory(new MockInformationFactory2());

        /**
         * @var RepositoryInformationService $repo_info_service
         */
        $repo_info_service = $this->dependency_injection_container->get(RepositoryInformationService::class);

        $repo_info_list = $repo_info_service->list();
        $this->assertCount(1, $repo_info_list);
        $repository_information = $repo_info_list[0];
        $this->assertInstanceOf(RepositoryInformation::class, $repository_information);
        $blocks = $repository_information->listInformationBlocks();
        $this->assertCount(3, $blocks);
        $this->assertInstanceOf(GitNameInformationBlock::class, $repository_information->listInformationBlocks()[0]);
        $this->assertInstanceOf(HelloWorldInformationBlock::class, $repository_information->listInformationBlocks()[1]);
        $this->assertInstanceOf(ErrorBlock::class, $repository_information->listInformationBlocks()[2]);
    }

    public function testList()
    {
        // Arrange
        $test_provider = new TestProvider();
        $test_provider->initialize();
        $this->dependency_injection_container = $test_provider->getDependencyInjectionContainer();
        $expected_number_of_repositories = 3;

        /**
         * @var RepositoryInformationService $repo_info_service
         */
        $repo_info_service = $this->dependency_injection_container->get(RepositoryInformationService::class);

        // Test with filter only_hello_world and nothing in cache
        $repo_info_list = $repo_info_service->list(TestProvider::TEST_FILTER_ONLY_HELLO_WORLD);
        $this->assertCount($expected_number_of_repositories, $repo_info_list);
        foreach ($repo_info_list as $repository_information) {
            $this->assertInstanceOf(RepositoryInformation::class, $repository_information);
            $this->assertInstanceOf(HelloWorldInformationBlock::class, $repository_information->listInformationBlocks()[0]);
        }

        // Test with filter only_hello_world and cache
        $repo_info_list = $repo_info_service->list(TestProvider::TEST_FILTER_ONLY_HELLO_WORLD);
        $this->assertCount($expected_number_of_repositories, $repo_info_list);
        foreach ($repo_info_list as $repository_information) {
            $this->assertInstanceOf(RepositoryInformation::class, $repository_information);
            $this->assertInstanceOf(HelloWorldInformationBlock::class, $repository_information->listInformationBlocks()[0]);
        }

        // Test with no filter (and cache)
        $repo_info_list = $repo_info_service->list(null);

        $this->assertCount($expected_number_of_repositories, $repo_info_list);
        foreach ($repo_info_list as $repository_information) {
            $this->assertInstanceOf(RepositoryInformation::class, $repository_information);
            $this->assertInstanceOf(GitNameInformationBlock::class, $repository_information->listInformationBlocks()[0]);
            $this->assertInstanceOf(HelloWorldInformationBlock::class, $repository_information->listInformationBlocks()[1]);
        }

        // Test with filter hello_world_and_git_name and nothing in cache
        $repo_info_list = $repo_info_service->list(TestProvider::TEST_FILTER_HELLO_WORLD_AND_GIT_NAME);
        $this->assertCount($expected_number_of_repositories, $repo_info_list);
        foreach ($repo_info_list as $repository_information) {
            $this->assertInstanceOf(RepositoryInformation::class, $repository_information);
            $this->assertInstanceOf(GitNameInformationBlock::class, $repository_information->listInformationBlocks()[0]);
            $this->assertInstanceOf(HelloWorldInformationBlock::class, $repository_information->listInformationBlocks()[1]);
        }
    }
}
