<?php

namespace CodeHqDk\RepositoryInformation;

use CodeHqDk\RepositoryInformation\Factory\InformationFactory;
use CodeHqDk\RepositoryInformation\Factory\RepositoryInformationFactory;
use CodeHqDk\RepositoryInformation\Model\Repository;
use CodeHqDk\RepositoryInformation\Registry\InformationFactoryRegistry;
use CodeHqDk\RepositoryInformation\Service\InformationBlockFilterService;
use CodeHqDk\RepositoryInformation\Service\ProviderDependencyService;
use CodeHqDk\RepositoryInformation\Services\InformationBlockService;
use CodeHqDk\RepositoryInformation\Services\RepositoryInformationService;
use CodeHqDk\RepositoryInformation\Services\SimpleInformationBlockFilterService;
use Slince\Di\Container;

class RepositoryInformationProvider implements ProviderDependencyService
{
    /**
     * @param string       $runtime_path    Path where a local copy of the repositores are placed, plus cached data
     * @param Repository[] $repository_list
     */
    public function __construct(
        private readonly string $runtime_path,
        private readonly array $repository_list,
        private readonly Container $dependency_injection_container
    ) {
    }

    public function addInformationFactoryProvider($informationFactoryProvider): void
    {
        $informationFactoryProvider->addFactory($this);
    }

    public function registerClassInDependencyContainer(string $fully_qualified_class_name): void
    {
        $this->dependency_injection_container->register($fully_qualified_class_name);
    }

    public function registerObjectInDependencyContainer($object): void
    {
        $this->dependency_injection_container->register(get_class($object), $object);
    }

    public function addInformactionFactoryToRegistry(InformationFactory $information_factory): void
    {
        $this->dependency_injection_container->get(InformationFactoryRegistry::class)->addFactory($information_factory);
    }
/*
    public function oldregister(Container $dependency_injection_container)
    {
        $this->registerDependencies($dependency_injection_container);
    }

    public function getDependencyInjectionContainer(): Container
    {
        return $this->dependecy_injection_container;
    }
*/
    public function registerDependencies(): void
    {
        $this->dependency_injection_container->register(InformationFactoryRegistry::class);
        $this->dependency_injection_container->register(InformationBlockService::class);

        $this->dependency_injection_container->register(RepositoryInformationFactory::class)->setArguments([
            'runtime_path' => $this->runtime_path,
            'information_factory_registry' => $this->dependency_injection_container->get(InformationFactoryRegistry::class),
            'information_block_filter_service' => $this->dependency_injection_container->get(InformationBlockFilterService::class),
        ]);

        $this->dependency_injection_container->register(RepositoryInformationService::class)->setArguments([
            'runtime_path' => $this->runtime_path,
            'repository_list' => $this->repository_list,
            'repository_information_factory' => $this->dependency_injection_container->get(RepositoryInformationFactory::class),
            'information_block_filter_service' => $this->dependency_injection_container->get(InformationBlockFilterService::class),
        ]);

        $this->registerRegistries();
        $this->registerFactories();
        $this->registerServices();
    }

    private function registerServices(): void
    {
    }

    private function registerFactories(): void
    {
    }

    private function registerRegistries(): void
    {
    }
}
