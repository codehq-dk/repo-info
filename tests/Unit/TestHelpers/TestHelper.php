<?php

namespace CodeHqDk\RepositoryInformation\Tests\Unit\TestHelpers;

use CodeHqDk\RepositoryInformation\InformationBlocks\GitNameInformationBlock;
use CodeHqDk\RepositoryInformation\Model\GitRepository;
use CodeHqDk\RepositoryInformation\Model\RepositoryCharacteristics;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TestHelper
{
    public static function createSampleInformationBlock(): GitNameInformationBlock
    {
        return new GitNameInformationBlock(
            'headline',
            'label',
            'value',
            0,
            'description'
        );
    }


    public static function getSampleRepositoryPath(): string
    {
        return dirname(__FILE__, 3) . '/data/sample-repository/repo-info-example-plugin';
    }

    public static function createSampleRepository(): GitRepository
    {
        return new GitRepository('https://github.com/codehq-dk/repo-info-example-plugin.git',
            new RepositoryCharacteristics(true, true, false, false));
    }

    public static function downloadSampleRepositoryIfNotExists($force_download = false): void
    {
        $sample_code_download_path = self::getSampleRepositoryPath();

        if ($force_download === true) {
            if (file_exists($sample_code_download_path)) {
                TestHelper::deleteFolder($sample_code_download_path);
            }
        }

        if (!file_exists($sample_code_download_path)) {
            $git_repository = TestHelper::createGitRepository();
            $git_repository->downloadCodeToLocalPath($sample_code_download_path);
        }
    }

    public static function deleteFolder($folderPath): void {
        $it = new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($folderPath);
    }

    private static function createGitRepository(): GitRepository
    {
        return new GitRepository(
            'https://github.com/codehq-dk/repo-info-example-plugin.git',
            new RepositoryCharacteristics(true, true, false, false));
    }
}
