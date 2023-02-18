<?php

namespace CodeHqDk\RepositoryInformation\Registry;

use CodeHqDk\RepositoryInformation\Factory\InformationFactory;

class InformationFactoryRegistry
{
    /**
     * @var InformationFactory[] $repository_to_factories ;
     */
    private array $information_factory_list = [];

    public function setFactories(array $information_factory_list): void
    {
        $this->information_factory_list = $information_factory_list;
    }

    public function addFactory(InformationFactory $factory): void
    {
        $this->information_factory_list[] = $factory;
    }

    /**
     * @return InformationFactory[]
     */
    public function listFactories(): array
    {
        return $this->information_factory_list;
    }
}
