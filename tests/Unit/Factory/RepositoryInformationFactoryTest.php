<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Factory;

use CodeHqDk\RepositoryInformation\Factory\GitInformationFactory;
use CodeHqDk\RepositoryInformation\Factory\RepositoryInformationFactory;
use CodeHqDk\RepositoryInformation\Model\RepositoryInformation;
use CodeHqDk\RepositoryInformation\Registry\InformationFactoryRegistry;
use CodeHqDk\RepositoryInformation\Services\InformationBlockService;
use CodeHqDk\RepositoryInformation\Services\SimpleInformationBlockFilterService;
use CodeHqDk\RepositoryInformation\Tests\Unit\TestHelpers\TestHelper;
use PHPUnit\Framework\TestCase;

class RepositoryInformationFactoryTest extends TestCase
{
    private function getTestRuntimePath(): string {
      return dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . "data";
    }

    public function testGetRepositoryInformation(): void
    {
        $information_factory_registry = new InformationFactoryRegistry();

        $information_factory_registry->addFactory(new GitInformationFactory);

        $information_block_service = new InformationBlockService($information_factory_registry);

        // Arrange + act
        $repository_information_factory = new RepositoryInformationFactory(
            $this->getTestRuntimePath(),
            $information_factory_registry,
            new SimpleInformationBlockFilterService($information_block_service)
        );

        $test_repository = TestHelper::createSampleRepository();
        $expected_path = $this->getTestRuntimePath() . RepositoryInformationFactory::LOCAL_REPOSITORY_COPY_PATH . $test_repository->getUuId();

        // Act
        $repository_information = $repository_information_factory->create($test_repository);
        $path = $repository_information_factory->getLocalCodePath($test_repository->getUuId());

        // Assert
        $this->assertEquals($expected_path, $path);

        $this->assertInstanceOf(RepositoryInformation::class, $repository_information);
    }
}
