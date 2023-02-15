<?php

namespace RepositoryInformation\Model;

class RepositoryCharacteristics extends RepositoryRequirements
{
    public static function fromArray(array $array): self
    {
        return new self(
            $array[parent::CONTAINS_PHP],
            $array[parent::COMPOSER_PACKAGE],
            $array[parent::CODECEPTION_USED],
            $array[parent::CONTAINS_JAVASCRIPT]
        );
    }

    public function toArray(): array
    {
        $array_to_return = [];

        foreach ($this->list as $key_name => $value) {
            $array_to_return[$key_name] = $value;
        }

        return $array_to_return;
    }
}
