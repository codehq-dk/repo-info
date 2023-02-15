<?php

namespace RepositoryInformation\Model\CodeRepositoryType;

use Kodus\Helpers\UUID;
use RepositoryInformation\Model\InformationBlock;
use RepositoryInformation\Model\RepositoryCharacteristics;
use RepositoryInformation\Model\Repository;
class GitRepository implements Repository
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $ssh_address,
        private readonly RepositoryCharacteristics $repository_characteristics
    ) {
    }

    public function downloadCodeToLocalPath(string $local_path): void
    {
        // TODO: Implement downloadCodeToLocalPath() method.
    }

    public function createRepositoryTypeInformationBlock(): InformationBlock
    {
        return new InformationBlock(
            self::REPOSITORY_TYPE_INFORMATION_BLOCK,
            'Repository type',
            __CLASS__,
            time(),
            "Repository type",
            "GIT"
        );
    }

    public function createRepositoryNameInformationBlock(): InformationBlock
    {
        $uuid = UUID::create();

        return new InformationBlock(
            self::REPOSITORY_NAME_INFORMATION_BLOCK,
            'Repository name',
            self::class, time(),
            'Name',
            $this->name,
            'UUID = ' . $uuid
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRepositoryCharacteristics(): RepositoryCharacteristics
    {
        return $this->repository_characteristics;
    }

    public function toArray(): array
    {
        return [
            'fully_qualified_class_name' => self::class,
            'id' => $this->id,
            'name' => $this->name,
            'ssh_address' => $this->ssh_address,
            'repository_characteristics' => $this->repository_characteristics->toArray()
        ];
    }

    public static function fromArray(array $array): Repository
    {
       return new self(
           $array['id'],
           $array['name'],
           $array['ssh_address'],
           RepositoryCharacteristics::fromArray($array['repository_characteristics'])
       );
    }
}
