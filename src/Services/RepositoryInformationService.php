<?php

namespace CodeHqDk\RepositoryInformation\Services;

use CodeHqDk\RepositoryInformation\Factory\RepositoryInformationFactory;
use CodeHqDk\RepositoryInformation\Model\InformationBlock;
use CodeHqDk\RepositoryInformation\Model\Repository;
use CodeHqDk\RepositoryInformation\Model\RepositoryInformation;
use CodeHqDk\RepositoryInformation\Service\InformationBlockFilterService;
use Exception;

/**
 * This class is the primary entry point to use the Repository Information ecosystem.
 *
 * Use the list method to get a list of RepositoryInformation models
 */
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
        private readonly RepositoryInformationFactory $repository_information_factory,
        private readonly InformationBlockFilterService $information_block_filter_service
    ) {}

    public function rebuildCompleteCache(): void
    {
        foreach ($this->repository_list as $repository)
        {
            $this->buildRepositoryInformationCache($repository);
        }
    }

    /**
     * @return RepositoryInformation[]
     */
    public function list(?string $filter_uuid = null): array
    {
        if (count($this->repository_list) === 0) {
            return [];
        }

        return $this->getRepositoryInformationList($filter_uuid);
    }

    /**
     * @return RepositoryInformation[]
     */
    private function getRepositoryInformationList(?string $filter_uuid = null): array
    {
        $repository_information_list = [];

        foreach ($this->repository_list as $repository)
        {
            /**
             * @var Repository $repository
             */
            $this->downloadCodeToLocalPathIfNeeded($repository);

            if ($this->doCachedDataExists($repository->getName(), $filter_uuid) === false) {
                $this->buildRepositoryInformationCache($repository, $filter_uuid);
            }

            $repository_information_list[] = $this->getRepositoryInformationFromCache($repository->getName(), $filter_uuid);
        }

        return $repository_information_list;
    }

    private function downloadCodeToLocalPathIfNeeded(Repository $repository): void
    {
        $download_path = $this->repository_information_factory->getLocalCodePath($repository->getName());

        if (file_exists($download_path)) {
            return;
        }

        $repository->downloadCodeToLocalPath($download_path);
    }

    private function doCachedDataExists(string $repository_id, ?string $filter_uuid = null): bool
    {
        $file = $this->getRepositoryInformationCacheFilename($repository_id, $filter_uuid);

        return file_exists($file);
    }

    private function getRepositoryInformationFromCache(string $repository_id, ?string $filter_uuid = null): RepositoryInformation
    {
        $file = $this->getRepositoryInformationCacheFilename($repository_id, $filter_uuid);
        $json_txt = file_get_contents($file);
        $repository_information_array = json_decode($json_txt, true);
        return RepositoryInformation::fromArray($repository_information_array);
    }

    private function getRepositoryInformationCacheFilename(string $repository_id, ?string $filter_uuid = null): string
    {
        if ($filter_uuid === null) {
            return $this->getCacheFolder() . "{$repository_id}-unfiltered.json";
        } else {
            $cache_hash = $this->getCacheHash($filter_uuid);
            return $this->getCacheFolder() . "{$repository_id}-{$filter_uuid}-{$cache_hash}.json";
        }
    }

    private function getCacheHash(?string $filter_uuid = null): string
    {
        $hash = '';

        foreach ($this->information_block_filter_service->getEnabledBlocks($filter_uuid) as $block_class_name) {
            $hash .= $block_class_name;
        }

        return md5($hash);
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

    /**
     * @throws Exception
     */
    private function buildRepositoryInformationCache(Repository $repository, ?string $filter_uuid = null): void
    {
        $repository_information = $this->repository_information_factory->create($repository, $filter_uuid);

        $json_txt = json_encode($repository_information->toArray());

        $file_name = $this->getRepositoryInformationCacheFilename($repository->getName(), $filter_uuid);

        $success = file_put_contents($file_name, $json_txt);

        if ($success === false) {
            throw new Exception('Could not write repository information cache file at: ' . $file_name);
        }
    }
}
