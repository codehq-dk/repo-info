<?php

namespace CodeHqDk\RepositoryInformation\Tests;

use CodeHqDk\RepositoryInformation\Services\RepositoryInformationService;
use PHPUnit\Framework\TestCase;

class RepositoryInformationServiceTest extends TestCase
{
    private object $dependency_injection_container;

    protected function setUp(): void
    {
        $test_provider = new TestProvider();
        $test_provider->initialize();
        $this->dependency_injection_container = $test_provider->getDependencyInjectionContainer();
    }

    public function testList()
    {
        /**
         * @var RepositoryInformationService $repo_info_service
         */
        $repo_info_service = $this->dependency_injection_container->get(RepositoryInformationService::class);

        $repo_info_list = $repo_info_service->list();
    }
}