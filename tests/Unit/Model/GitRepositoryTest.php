<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\Model;

use CodeHqDk\RepositoryInformation\Model\GitRepository;
use CodeHqDk\RepositoryInformation\Model\RepositoryCharacteristics;
use CodeHqDk\RepositoryInformation\Tests\Unit\TestHelpers\TestHelper;
use Exception;
use Kodus\Helpers\UUID;
use PHPUnit\Framework\TestCase;

class GitRepositoryTest extends TestCase
{
    public function testExceptionThrownOnInvalidGitCloneAddressCaseA(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The git clone address 'non-exising-git-clone-address' is not a valid url");
        (new GitRepository('non-exising-git-clone-address'))->downloadCodeToLocalPath('invalid-path');
    }

    public function testExceptionThrownOnInvalidGitCloneAddressCaseB(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed at downloading 'http://non-exising-git-clone-address' repository to local path 'invalid-path' (Error message: Running command '/usr/bin/git clone http://non-exising-git-clone-address invalid-path' failed with result code 128 - 'INVALID_ARGUMENT_TO'");
        (new GitRepository('http://non-exising-git-clone-address'))->downloadCodeToLocalPath('invalid-path');
    }

    public function testDownloadCodeToLocalPath(): void
    {
        // Arrange + act
        TestHelper::downloadSampleRepositoryIfNotExists(true);

        // Assert
        $this->assertDirectoryExists(TestHelper::getSampleRepositoryPath());
        $this->assertNotEmpty(glob(TestHelper::getSampleRepositoryPath() . '/*'));
    }

    public function testGetNameMethod(): void
    {
        // Arrange + act
        $repository = TestHelper::createSampleRepository();

        // Assert
        $this->assertSame('repo-info-example-plugin.git', $repository->getName());
    }

    public function testGetIdMethod(): void
    {
        // Arrange + act
        $repository = TestHelper::createSampleRepository();

        // Assert
        $this->assertTrue(UUID::isValid($repository->getUuId()));
    }

    public function testGetRepositoryCharacteristics(): void
    {
        // Arrange + act
        $repository = TestHelper::createSampleRepository();

        // Assert
        $this->assertInstanceOf(RepositoryCharacteristics::class, $repository->getRepositoryCharacteristics());
    }

    public function testFromArrayToArray()
    {
        // Arrange
        $repository = TestHelper::createSampleRepository();

        // Act
        $repository_array = $repository->toArray();
        $repository_from_array = GitRepository::fromArray($repository_array);

        // Assert
        $this->assertSame($repository_array, $repository_from_array->toArray());
    }
}
