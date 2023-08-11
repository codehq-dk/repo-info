<?php

namespace CodeHqDk\RepositoryInformation\Factory;

use CodeHqDk\LinuxBashHelper\Bash;
use CodeHqDk\RepositoryInformation\InformationBlocks\GitNameInformationBlock;
use CodeHqDk\RepositoryInformation\Model\RepositoryRequirements;
use Lcobucci\Clock\Clock;
use Lcobucci\Clock\SystemClock;

class GitInformationFactory implements InformationFactory
{
    public const DEFAULT_ENABLED_BLOCKS = [
        GitNameInformationBlock::class
    ];

    public function __construct(
        private ?Clock $clock = null
    ) {
        if ($this->clock === null) {
            $this->clock = SystemClock::fromSystemTimezone();
        }
    }

    public function createBlocks(
        string $local_path_to_code,
        array $information_block_types_to_create = self::DEFAULT_ENABLED_BLOCKS
    ): array {

        if (!in_array(GitNameInformationBlock::class, $information_block_types_to_create)) {
            return [];
        }

        $git_name = $this->getGitNameFromFolder($local_path_to_code);

        return [
            new GitNameInformationBlock(
            'Git repository name',
            'Name',
            $git_name,
            $this->clock->now()->getTimestamp(),
            '(Git name was extracted from folder name)',
            'This is information from the Git Information Factory'
            )
        ];
    }

    public function getRepositoryRequirements(): RepositoryRequirements
    {
        return new RepositoryRequirements(false, false, false, false);
    }

    public function listAvailableInformationBlocks(): array
    {
        return [
            GitNameInformationBlock::class
        ];
    }

    private function getGitNameFromFolder(string $local_path_to_code): string
    {
        $command_to_run = "cd {$local_path_to_code};basename `git rev-parse --show-toplevel`";

        $git_name = Bash::runCommand($command_to_run);

        return $git_name[0];
    }
}
