<?php

namespace CodeHqDk\RepositoryInformation\Model;

use CodeHqDk\LinuxBashHelper\Bash;
use CodeHqDk\LinuxBashHelper\Environment;
use CodeHqDk\LinuxBashHelper\Exception\LinuxBashHelperException;
use CodeHqDk\RepositoryInformation\Exception\RepositoryInformationException;
use Exception;
use InvalidArgumentException;
use Kodus\Helpers\UUID;

/**
 * Notice. This class requires that an installation of git is present on the server
 */
class GitRepository implements Repository
{
    private string $uuid;

    /**
     * $param string $git_clone_address https / ssh address
     *
     * Public repository  -> Use the https address
     * Private repository -> use the ssh address (This requires that your server have valid ssh keys registered at Github)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly string $git_clone_address,
        private readonly ?RepositoryCharacteristics $repository_characteristics = null,
        ?string $uuid = null
    ) {
        $this->uuid = $uuid ?? UUID::create();
    }

    /**
     * @throws Exception
     */
    public function downloadCodeToLocalPath(string $local_path): void
    {
        if (filter_var($this->git_clone_address, FILTER_VALIDATE_URL) === false) {
            throw new RepositoryInformationException("The git clone address '{$this->git_clone_address}' is not a valid url");
        }

        $git_path = Environment::getGitPath();

        try {
            $command = $git_path . " clone {$this->git_clone_address} {$local_path}";
            Bash::runCommand($command);
        } catch (LinuxBashHelperException $exception) {
            throw new Exception("Failed at downloading '{$this->git_clone_address}' repository to local path '{$local_path}' (Error message: {$exception->getMessage()}");
        }
    }

    public function getUuId(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        $exploded_address = explode('/', $this->git_clone_address);
        return end($exploded_address);
    }

    public function getRepositoryCharacteristics(): RepositoryCharacteristics
    {
        return $this->repository_characteristics;
    }

    public function toArray(): array
    {
        return [
            'fully_qualified_class_name' => self::class,
            'uuid' => $this->uuid,
            'ssh_address' => $this->git_clone_address,
            'repository_characteristics' => $this->repository_characteristics->toArray()
        ];
    }

    public static function fromArray(array $array): Repository
    {
       return new self(
           $array['ssh_address'],
           RepositoryCharacteristics::fromArray($array['repository_characteristics']),
           $array['uuid']
       );
    }
}
