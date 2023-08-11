<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Factory;

use CodeHqDk\RepositoryInformation\Factory\GitInformationFactory;
use CodeHqDk\RepositoryInformation\InformationBlocks\GitNameInformationBlock;
use CodeHqDk\RepositoryInformation\Model\RepositoryRequirements;
use CodeHqDk\RepositoryInformation\Tests\Unit\TestHelpers\TestHelper;
use Lcobucci\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;

/**
 * @group whitelisted
 */
class GitInformationFactoryTest extends TestCase
{
    private FrozenClock $clock;

    protected function setup(): void
    {
        $this->clock = FrozenClock::fromUTC();
    }

    public function testListAvailable(): void
    {
        $factory = new GitInformationFactory($this->clock);
        $expected = [GitNameInformationBlock::class];
        $this->assertEquals($expected, $factory->listAvailableInformationBlocks());
    }

    public function testGetRepositoryRequirements(): void
    {
        $factory = new GitInformationFactory($this->clock);
        $this->assertInstanceOf(RepositoryRequirements::class, $factory->getRepositoryRequirements());
    }

    public function testCreateBlocks(): void
    {
        $factory = new GitInformationFactory($this->clock);

        $expected_block = new GitNameInformationBlock(
            'Git repository name',
            'Name',
            'repo-info-example-plugin',
            $this->clock->now()->getTimestamp(),
            'Cannot build code coverage information - .../bin/phpunit or phpunit.xml is missing',
            'This is information from the Git Information Factory',
        );

        TestHelper::downloadSampleRepositoryIfNotExists();
        $actual_blocks = $factory->createBlocks(TestHelper::getSampleRepositoryPath());

        /**
         * @var GitNameInformationBlock $actual_block
         */
        $actual_block = array_pop($actual_blocks);

        $this->assertEquals($expected_block->getHeadline(), $actual_block->getHeadline());
        $this->assertEquals($expected_block->getLabel(), $actual_block->getLabel());
        $this->assertEquals($expected_block->getValue(), $actual_block->getValue());
        $this->assertEquals($expected_block->getModifiedTimestamp(), $actual_block->getModifiedTimestamp());
        $this->assertEquals($expected_block->getInformationOrigin(), $actual_block->getInformationOrigin());
    }
}
