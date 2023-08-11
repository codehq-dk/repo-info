<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Mock;

use CodeHqDk\RepositoryInformation\Factory\GitInformationFactory;
use CodeHqDk\RepositoryInformation\Model\RepositoryRequirements;

class MockInformationFactory1 extends GitInformationFactory
{
    // Set a fake requirement to test RepositoryInformationService
    public function getRepositoryRequirements(): RepositoryRequirements
    {
        return new RepositoryRequirements(false, false, false, true);
    }
}
