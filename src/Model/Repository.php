<?php

namespace CodeHqDk\RepositoryInformation\Model;

use Exception;

interface Repository
{
    public const REPOSITORY_NAME_INFORMATION_BLOCK = 'Repository name';
    public const REPOSITORY_TYPE_INFORMATION_BLOCK = 'Repository type';

    /**
     * @throws Exception An exception is thrown if download fails
     */
    public function downloadCodeToLocalPath(string $local_path): void;

    public function createRepositoryTypeInformationBlock(): InformationBlock;

    public function createRepositoryNameInformationBlock(): InformationBlock;

    public function getId(): string;

    public function getRepositoryCharacteristics(): RepositoryCharacteristics;

    public function toArray(): array;

    public static function fromArray(array $array): self;
}
