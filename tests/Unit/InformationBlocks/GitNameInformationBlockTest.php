<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\InformationBlocks;

use CodeHqDk\RepositoryInformation\InformationBlocks\GitNameInformationBlock;
use PHPUnit\Framework\TestCase;

/**
 * @group whitelisted
 */
class GitNameInformationBlockTest extends TestCase
{
    public function testConstructionAndGetters()
    {
        $block = new GitNameInformationBlock(
            $expected_headline = 'expected headline',
            $expected_label = 'expected label',
            $expected_value = 'expected value',
            $expected_time_stamp = time(),
            $expected_details = 'expected details',
            $expected_information_origin = 'expected information origin'
        );

        $this->assertEquals($expected_headline, $block->getHeadline());
        $this->assertEquals($expected_label, $block->getLabel());
        $this->assertEquals($expected_value, $block->getValue());
        $this->assertEquals($expected_time_stamp, $block->getModifiedTimestamp());
        $this->assertEquals($expected_details, $block->getDetails());
        $this->assertEquals($expected_information_origin, $block->getInformationOrigin());

        $this->assertEquals(GitNameInformationBlock::class, $block->getInfoTypeId());
    }

    public function testSerialization()
    {
        $original_block = new GitNameInformationBlock(
            'expected headline',
            'expected label',
            'expected value',
            time(),
            'expected details',
            'expected information origin'
        );

        $block_as_array = $original_block->toArray();

        $new_block = GitNameInformationBlock::fromArray($block_as_array);

        $this->assertEquals($original_block, $new_block);
    }
}
