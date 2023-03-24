<?php

namespace CodeHqDk\RepositoryInformation\Services;

use CodeHqDk\RepositoryInformation\Registry\InformationFactoryRegistry;

class InformationBlockService
{
    public function __construct(private readonly InformationFactoryRegistry $information_factory_registry)
    {
    }

    /**
     * @return array A list of available Information blocks, listed by their fully qualified class names
     */
    public function listAvailable(): array
    {
        $list = [];

        foreach ($this->information_factory_registry->listFactories() as $factory) {
            $list = array_merge($list, $factory->listAvailableInformationBlocks());
        }

        return $list;
    }
}