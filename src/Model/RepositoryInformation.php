<?php

namespace CodeHqDk\RepositoryInformation\Model;

use Exception;

/**
 * This object will supply the repository plus a list of information blocks
 */
class RepositoryInformation
{
    /**
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
        if (!isset($array['repository']['fully_qualified_class_name'])) {
            var_dump($array);
            throw new Exception('Cannot build object from array - array key `fully_qualified_class_name` is missing');
        }
        $class_name = $array['repository']['fully_qualified_class_name'];
        $repository = $class_name::fromArray($array['repository']);

        return new self($repository, self::informationBlockFromArray($array['information_block_list']));
    }

    public function toArray(): array
    {
        return
            [
                'repository' => $this->repository->toArray(),
                'information_block_list' => $this->informationBlocksToArray()
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
            $information_block_list[] = InformationBlock::fromArray($information_block_array);
        }

        return $information_block_list;
    }
}
