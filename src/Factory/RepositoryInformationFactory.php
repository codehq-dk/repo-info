<?php

namespace CodeHqDk\RepositoryInformation\Factory;

use CodeHqDk\RepositoryInformation\InformationBlocks\RepositoryNameInformationBlock;
use CodeHqDk\RepositoryInformation\InformationBlocks\RepositoryTypeInformationBlock;
use CodeHqDk\RepositoryInformation\Model\RepositoryInformation;
use CodeHqDk\RepositoryInformation\Model\Repository;
use CodeHqDk\RepositoryInformation\Registry\InformationFactoryRegistry;
use CodeHqDk\RepositoryInformation\Service\InformationBlockFilterService;

/**
 * This class is used by the RepositoryInformationService to build instances of the RepositoryInformation model
 *
 * The class will use
 * - InformationFactoryRegistry to know which Information Blocks are bootstrapped
 * - The bootstrapped implementation of InformationBlockFilterService to allow a filtering of blocks
 * - The RepositoryCharacteristics and the RepositoryRequirements from the Repository to only include relevant info blocks
 *
 * @internal
 */
class RepositoryInformationFactory
{
    public function __construct(
        private readonly string $runtime_path,
        private readonly InformationFactoryRegistry $information_factory_registry,
        private readonly InformationBlockFilterService $information_block_filter_service
    ) {
    }

    public function create(Repository $repository, ?string $filter_uuid = null): RepositoryInformation
    {
        $information_block_list = $this->populateInformationBlocks($repository, $filter_uuid);

        return new RepositoryInformation($repository, $information_block_list);
    }

    public function getLocalCodePath(string $repository_id) : string
    {
        return $this->runtime_path . DIRECTORY_SEPARATOR . 'repository-local-copy' . DIRECTORY_SEPARATOR . $repository_id;
    }

    private function populateInformationBlocks(Repository $repository, ?string $filter_uuid = null): array
    {
        $information_block_list = $this->addNameAndTypeInformationBlocks($repository, $filter_uuid);

        return $this->addInformationBlocksFromFactories($information_block_list, $repository, $filter_uuid);
    }

    private function addNameAndTypeInformationBlocks(Repository $repository, ?string $filter_uuid = null): array
    {
        $information_block_list = [];

        if (in_array(RepositoryNameInformationBlock::class, $this->information_block_filter_service->getEnabledBlocks($filter_uuid))) {
            $information_block_list[] = $repository->createRepositoryNameInformationBlock();
        }

        if (in_array(RepositoryTypeInformationBlock::class, $this->information_block_filter_service->getEnabledBlocks($filter_uuid))) {
            $information_block_list[] = $repository->createRepositoryTypeInformationBlock();
        }

        return $information_block_list;
    }

    private function addInformationBlocksFromFactories(array $information_block_list, Repository $repository, ?string $filter_uuid = null): array{
        $local_code_path = $this->getLocalCodePath($repository->getId());

        $information_factory_list = $this->information_factory_registry->listFactories();
        $characteristics_list = $repository->getRepositoryCharacteristics()->list();

        foreach ($information_factory_list as $information_factory) {
            $requirements_list = $information_factory->getRepositoryRequirements()->list();

            if ($this->doCharacteristicsMatchRequirements($requirements_list, $characteristics_list) === true) {
                $information_block_types_to_create = $this->listInformationBlockTypesToCreate($information_factory, $filter_uuid);
                $information_block_list = array_merge($information_block_list, $information_factory->createBlocks($local_code_path, $information_block_types_to_create));
            }
        }

        return $information_block_list;
    }

    private function listInformationBlockTypesToCreate(InformationFactory $information_factory, ?string $filter_uuid = null): array
    {
        $enabled_info_block_types = $this->information_block_filter_service->getEnabledBlocks($filter_uuid);
        $factory_available_block_types = $information_factory->listAvailableInformationBlocks();

        return array_intersect($enabled_info_block_types, $factory_available_block_types);
    }

    private function doCharacteristicsMatchRequirements(array $requirements_list, array $characteristics_list): bool
    {
        foreach ($requirements_list as $requirement => $required) {
            if ($required && $characteristics_list[$requirement] === false) {
                return false;
            }
        }

        return true;
    }
}
