<?php

namespace RepositoryInformation\Model;

interface Repository
{
    public const REPOSITORY_NAME_INFORMATION_BLOCK = 'Repository name';
    public const REPOSITORY_TYPE_INFORMATION_BLOCK = 'Repository type';

    public function __construct(
        string $id,
        string $name,
        string $ssh_address,
        RepositoryCharacteristics $repository_characteristics
    );

    public function downloadCodeToLocalPath(string $local_path): void;

    public function createRepositoryTypeInformationBlock(): InformationBlock;

    public function createRepositoryNameInformationBlock(): InformationBlock;

    public function getId(): string;

    public function getRepositoryCharacteristics(): RepositoryCharacteristics;

    public function toArray(): array;

    public static function fromArray(array $array): self;
}
