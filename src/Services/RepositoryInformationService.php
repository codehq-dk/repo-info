<?php

namespace RepositoryInformation\Services;

use Exception;
use RepositoryInformation\Factory\RepositoryInformationFactory;
use RepositoryInformation\Model\RepositoryInformation;
use RepositoryInformation\Model\Repository;

class RepositoryInformationService
{
    /**
     * @param string                       $runtime_path
     * @param array                        $repository_list
     * @param RepositoryInformationFactory $repository_information_factory
     */
    public function __construct(
        private readonly string $runtime_path,
        private readonly array $repository_list,
        private readonly RepositoryInformationFactory $repository_information_factory
    ) {}

    /**
     * @return RepositoryInformation[]
     */
    public function list(): array
    {
        if (count($this->repository_list) === 0) {
            return [];
        }

        return $this->getRepositoryInformationList();
    }

    /**
     * @return RepositoryInformation[]
     */
    private function getRepositoryInformationList(): array
    {
        $repository_information_list = [];

        foreach ($this->repository_list as $repository)
        {
            if ($this->doCachedDataExists($repository->getId()) === false) {
                $this->buildRepositoryInformationCache($repository);
            }

            $repository_information_list[] = $this->getRepositoryInformationFromCache($repository->getId());
        }

        return $repository_information_list;
    }

    private function doCachedDataExists(string $repository_id): bool
    {
        $file = $this->getRepositoryInformationCacheFilename($repository_id);

        return file_exists($file);
    }

    private function getRepositoryInformationFromCache(string $repository_id): RepositoryInformation
    {
        $file = $this->getRepositoryInformationCacheFilename($repository_id);
        $json_txt = file_get_contents($file);
        $repository_information_array = json_decode($json_txt, true);
        return RepositoryInformation::fromArray($repository_information_array);
    }

    private function getRepositoryInformationCacheFilename(string $repository_id): string
    {
        return $this->getCacheFolder() . $repository_id . '.json';
    }

    private function getCacheFolder(): string
    {
        $cache_folder=  $this->runtime_path . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;

        if (file_exists($cache_folder) === false) {
            if (mkdir($cache_folder, 0777, true) === false)
            {
                throw new Exception('Cannot create cache folder at location : ' . $cache_folder);
            }
        }

        return $cache_folder;
    }

    public function rebuildCompleteCache(): void
    {
        foreach ($this->repository_list as $repository)
        {
            $this->buildRepositoryInformationCache($repository);
        }
    }

    /**
     * @throws Exception
     */
    private function buildRepositoryInformationCache(Repository $repository): void
    {
        $repository_information = $this->repository_information_factory->create($repository);

        $json_txt = json_encode($repository_information->toArray());

        $file_name = $this->getRepositoryInformationCacheFilename($repository->getId());

        $success = file_put_contents($file_name, $json_txt);

        if ($success === false) {
            throw new Exception('Could not write repository information cache file at: ' . $file_name);
        }
    }
}
