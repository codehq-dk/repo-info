<?php

namespace RepositoryInformation\Factory;

use RepositoryInformation\Model\RepositoryInformation;
use RepositoryInformation\Model\Repository;
use RepositoryInformation\Registry\InformationFactoryRegistry;
class RepositoryInformationFactory
{
    public function __construct(
        private readonly string $runtime_path,
        private readonly InformationFactoryRegistry $information_factory_registry
    ) {
    }

    public function create(Repository $repository): RepositoryInformation
    {
        $final_local_code_path = $this->getLocalCodePath($repository->getId());
        $repository->downloadCodeToLocalPath($final_local_code_path);
        $information_block_list = $this->populateInformationBlocks($repository);

        return new RepositoryInformation($repository, $information_block_list);
    }

    private function populateInformationBlocks(Repository $repository): array
    {
        $final_local_code_path = $this->getLocalCodePath($repository->getId());
        $information_block_list[] = $repository->createRepositoryNameInformationBlock();
        $information_block_list[] = $repository->createRepositoryTypeInformationBlock();

        $information_factory_list = $this->information_factory_registry->listFactories();
        $characteristics_list = $repository->getRepositoryCharacteristics()->list();

        foreach ($information_factory_list as $information_factory) {

            $requirements_list = $information_factory->getRepositoryRequirements()->list();

            if ($this->doCharacteristicsMatchRequirements($requirements_list, $characteristics_list) === true) {
                $information_block_list = array_merge($information_block_list, $information_factory->createBlocks($final_local_code_path));
            }
        }

        return $information_block_list;
    }

    private function getLocalCodePath(string $repository_id) : string
    {
        return $this->runtime_path . DIRECTORY_SEPARATOR . 'repository-local-copy' . DIRECTORY_SEPARATOR . $repository_id;
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
