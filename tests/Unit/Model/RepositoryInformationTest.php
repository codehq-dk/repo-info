<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Model;

use CodeHqDk\RepositoryInformation\Model\RepositoryInformation;
use CodeHqDk\RepositoryInformation\Tests\Unit\TestHelpers\TestHelper;
use Exception;
use PHPUnit\Framework\TestCase;

class RepositoryInformationTest extends TestCase
{

    private function createRepositoryInformationModel(): RepositoryInformation
    {
        return new RepositoryInformation(
            TestHelper::createSampleRepository(),
            [TestHelper::createSampleInformationBlock()]
        );
    }

    public function testGetRepositoryMethod(): void
    {
        // Arrange + act
        $repository_information = $this->createRepositoryInformationModel();

        // Assert
        $this->assertEquals(TestHelper::createSampleRepository()->getName(),
            $repository_information->getRepository()->getName());
        $this->assertEquals(TestHelper::createSampleRepository()->getRepositoryCharacteristics(),
            $repository_information->getRepository()->getRepositoryCharacteristics());
    }

    public function testListInformationBlocksMethod(): void
    {
        // Arrange + act
        $repository_information = $this->createRepositoryInformationModel();

        // Assert
        $this->assertEquals([TestHelper::createSampleInformationBlock()],
            $repository_information->listInformationBlocks());
    }

    public function testFromArrayToArray(): void
    {
        // Arrange + act
        $repository_information = $this->createRepositoryInformationModel();

        // Assert
        $this->assertEquals(
            $repository_information,
            RepositoryInformation::fromArray($repository_information->toArray())
        );
    }

    public function testFromArrayException(): void
    {
        $array = [
            RepositoryInformation::REPOSITORY_ARRAY_KEY => '',
            RepositoryInformation::INFORMATION_BLOCK_LIST_ARRAY_KEY => ''
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Cannot build object from array - array key `fully_qualified_class_name` is missing");
        RepositoryInformation::fromArray($array);
    }
}
