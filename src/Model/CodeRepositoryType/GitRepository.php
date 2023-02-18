<?php

namespace CodeHqDk\RepositoryInformation\Model\CodeRepositoryType;

use CodeHqDk\LinuxBashHelper\Bash;
use CodeHqDk\LinuxBashHelper\Environment;
use CodeHqDk\LinuxBashHelper\Exception\LinuxBashHelperException;
use Exception;
use Kodus\Helpers\UUID;
use CodeHqDk\RepositoryInformation\Model\InformationBlock;
use CodeHqDk\RepositoryInformation\Model\RepositoryCharacteristics;
use CodeHqDk\RepositoryInformation\Model\Repository;

/**
 * Notice. This class requires a installation of git is present on the server
 */
class GitRepository implements Repository
{
    private const VALID_ID_REGEX = '/^[0-9a-z-]+/';

    /**
     * $param string $git_clone_address https / ssh address
     *
     * Public repository  -> Use the https address
     * Private repository -> use the ssh address (This requires that your server have valid ssh keys registered at Github)
     *
     * @throws Exception
     */
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $git_clone_address,
        private readonly RepositoryCharacteristics $repository_characteristics
    ) {
        $this->throwExceptionOnInvalidId($this->id);
    }

    /**
     * @throws Exception
     */
    public function downloadCodeToLocalPath(string $local_path): void
    {
        $git_path = Environment::getGitPath();

        try {
            $command = $git_path . " clone {$this->git_clone_address} {$local_path}";
            Bash::runCommand($command);
        } catch (LinuxBashHelperException) {
            throw new Exception("Failed at downloading '{$this->name}' repository to local path '{$local_path}'");
        }
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
            'ssh_address' => $this->git_clone_address,
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

    private function throwExceptionOnInvalidId(string $id): void
    {
        preg_match(self::VALID_ID_REGEX, $id, $matches);

        if (count($matches) === 0 || $matches[0] !== $id) {
            throw new Exception("Repository id value '{$id}' is not a valid format");
        }
    }
}
