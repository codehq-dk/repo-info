<?php

namespace CodeHqDk\RepositoryInformation\Factory;

use CodeHqDk\RepositoryInformation\InformationBlocks\ErrorBlock;
use CodeHqDk\RepositoryInformation\InformationBlocks\RequirementsNotMetBlock;
use CodeHqDk\RepositoryInformation\Model\Repository;
use CodeHqDk\RepositoryInformation\Model\RepositoryInformation;
use CodeHqDk\RepositoryInformation\Registry\InformationFactoryRegistry;
use CodeHqDk\RepositoryInformation\Service\InformationBlockFilterService;
use Exception;

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
    public const LOCAL_REPOSITORY_COPY_PATH = '/repository-local-copy/';

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
        return $this->runtime_path . self::LOCAL_REPOSITORY_COPY_PATH . $repository_id;
    }

    private function populateInformationBlocks(Repository $repository, ?string $filter_uuid = null): array
    {
        $information_block_list = [];

        return $this->addInformationBlocksFromFactories($information_block_list, $repository, $filter_uuid);
    }

    private function addInformationBlocksFromFactories(array $information_block_list, Repository $repository, ?string $filter_uuid = null): array{
        $local_code_path = $this->getLocalCodePath($repository->getName());

        $information_factory_list = $this->information_factory_registry->listFactories();
        $characteristics_list = $repository->getRepositoryCharacteristics()->list();

        foreach ($information_factory_list as $information_factory) {
            $requirements_list = $information_factory->getRepositoryRequirements()->list();

            if ($this->doCharacteristicsMatchRequirements($requirements_list, $characteristics_list) === true) {

                $information_block_types_to_create = $this->listInformationBlockTypesToCreate($information_factory, $filter_uuid);

                try {
                    $blocks_created = $information_factory->createBlocks($local_code_path, $information_block_types_to_create);
                    $this->throwExceptionIfFilterIsNotRespected($blocks_created, $information_block_types_to_create);
                } catch (Exception $exception) {
                    $blocks_created = [$this->createErrorBlock($exception)];
                }

                $information_block_list = array_merge($information_block_list, $blocks_created);
            } else {
                $information_block_list[] = $this->createRequirementsNotMetBlock();
            }
        }

        return $information_block_list;
    }

    private function createErrorBlock(Exception $exception): ErrorBlock
    {
        return new ErrorBlock(
            'An error occurred while creating the information block',
            'Exception message',
            $exception->getMessage(),
            time(),
            'Trace: ' . $exception->getTraceAsString(),
            self::class
        );
    }

    private function createRequirementsNotMetBlock(): RequirementsNotMetBlock
    {
        return new RequirementsNotMetBlock(
            'Requirements not met for the plugin in this repository',
            '',
            '',
            time(),
            '',
            self::class
        );
    }

    /**
     * @throws Exception
     */
    private function throwExceptionIfFilterIsNotRespected(array $blocks_created, array $information_block_types_to_create): void
    {
        foreach ($blocks_created as $block_created) {
            if (!in_array(get_class($block_created), $information_block_types_to_create)) {
                throw new Exception('The information block ' . get_class($block_created) . ' was not expected to be created, please make sure your factory respect the $information_block_types_to_create parameter');
            }
        }
    }

    private function listInformationBlockTypesToCreate(InformationFactory $information_factory, ?string $filter_uuid = null): array
    {
        $enabled_info_block_types = $this->information_block_filter_service->getEnabledBlocks($filter_uuid);
        $factory_available_block_types = $information_factory->listAvailableInformationBlocks();

        return array_intersect($enabled_info_block_types, $factory_available_block_types);
    }

    private function doCharacteristicsMatchRequirements(array $requirements_list, ?array $characteristics_list): bool
    {
        // If no code is available return true to try and let the factory create the information blocks
        if ($characteristics_list === null) {
            return true;
        }

        foreach ($requirements_list as $requirement => $required) {
            if ($required && $characteristics_list[$requirement] === false) {
                return false;
            }
        }

        return true;
    }
}
