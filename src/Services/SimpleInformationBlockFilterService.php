<?php

namespace CodeHqDk\RepositoryInformation\Services;

use CodeHqDk\RepositoryInformation\Service\InformationBlockFilterService;

/**
 * Simple implementation of the InformationBlockFilterService interface
 *
 * Create instances of this object like this:
 *
 * $filter_service = new SimpleInformationBlockFilterService(
 *      new InformationBlockService(),
 *      [
 *          'uuid' =>
 *              [
 *                  HelloWorldInformationBlock::class,
 *                  AnotherInformationBlock::class,
 *                  etc.
 *              ]
 *      ]
 * );
 */
class SimpleInformationBlockFilterService implements InformationBlockFilterService
{
    public function __construct(
        private readonly InformationBlockService $information_block_service,
        private readonly array $uuid_to_information_block_class_name_list_map = []
    ) {
    }

    public function getEnabledBlocks(?string $uuid = null): array
    {
        if (key_exists($uuid, $this->uuid_to_information_block_class_name_list_map)) {
            return $this->uuid_to_information_block_class_name_list_map[$uuid];
        } else {
            return $this->information_block_service->listAvailable();
        }
    }
}
