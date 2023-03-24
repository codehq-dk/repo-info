<?php

namespace CodeHqDk\RepositoryInformation\Model;

use Exception;

/**
 * This object will supply the repository plus a list of information blocks
 */
class RepositoryInformation
{
    public const REPOSITORY_ARRAY_KEY = 'repository';
    public const INFORMATION_BLOCK_LIST_ARRAY_KEY = 'information_block_list';

    /**
     * Do not new instances of this class up yourself. The RepositoryInformationFactory should do that
     *
     * @param Repository         $repository
     * @param InformationBlock[] $information_block_list
     */
    public function __construct(
        private readonly Repository $repository,
        private readonly array $information_block_list
    ) {}

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * @return InformationBlock[]
     */
    public function listInformationBlocks(): array
    {
        return $this->information_block_list;
    }

    /**
     * @throws Exception
     */
    public static function fromArray(array $array): self
    {
        if (!isset($array[self::REPOSITORY_ARRAY_KEY]['fully_qualified_class_name'])) {
            throw new Exception('Cannot build object from array - array key `fully_qualified_class_name` is missing');
        }
        $class_name = $array[self::REPOSITORY_ARRAY_KEY]['fully_qualified_class_name'];
        $repository = $class_name::fromArray($array[self::REPOSITORY_ARRAY_KEY]);

        return new self($repository, self::informationBlockFromArray($array[self::INFORMATION_BLOCK_LIST_ARRAY_KEY]));
    }

    public function toArray(): array
    {
        return
            [
                self::REPOSITORY_ARRAY_KEY => $this->repository->toArray(),
                self::INFORMATION_BLOCK_LIST_ARRAY_KEY => $this->informationBlocksToArray()
            ];
    }

    private function informationBlocksToArray(): array
    {
        $list = [];

        foreach ($this->information_block_list as $information_block)
        {
            $list[] = $information_block->toArray();
        }

        return $list;
    }

    /**
     * @param array $information_block_list_array
     *
     * @return InformationBlock[]
     */
    private static function informationBlockFromArray(array $information_block_list_array): array
    {
        $information_block_list = [];
        foreach ($information_block_list_array as $information_block_array) {
            $class = $information_block_array['info_type'];
            $information_block_list[] = $class::fromArray($information_block_array);
        }

        return $information_block_list;
    }
}
