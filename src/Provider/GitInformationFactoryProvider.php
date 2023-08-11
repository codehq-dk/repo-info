<?php

namespace CodeHqDk\RepositoryInformation\Provider;

use CodeHqDk\RepositoryInformation\Factory\GitInformationFactory;
use CodeHqDk\RepositoryInformation\Factory\InformationFactoryProvider;
use CodeHqDk\RepositoryInformation\Service\ProviderDependencyService;

/**
 * @internal
 *
 * This provider is always bootstrapped by default
 */
class GitInformationFactoryProvider implements InformationFactoryProvider
{
    public function addFactory(ProviderDependencyService $provider_dependency_service): void
    {
        $provider_dependency_service->registerClassInDependencyContainer(GitInformationFactory::class);
        $provider_dependency_service->addInformationFactoryToRegistry(new GitInformationFactory());
    }
}
