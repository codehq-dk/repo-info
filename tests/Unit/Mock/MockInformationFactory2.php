<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Mock;

use CodeHqDk\RepositoryInformation\Factory\GitInformationFactory;
use CodeHqDk\RepositoryInformation\Model\RepositoryRequirements;
use Exception;

class MockInformationFactory2 extends GitInformationFactory
{
    // Fake an error in a factory to test gracefully handling of errors
    public function createBlocks(
        string $local_path_to_code,
        array $information_block_types_to_create = self::DEFAULT_ENABLED_BLOCKS
    ): array {
        throw new Exception();
    }
}
