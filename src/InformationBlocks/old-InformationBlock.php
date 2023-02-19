<?php

namespace CodeHqDk\RepositoryInformation\Model;

class OldInformationBlock
{
    public function __construct(
        private readonly string $info_type,
        private readonly string $headline,
        private readonly string $information_origin,
        private readonly int $modified_timestamp,
        private readonly string $label,
        private readonly string $value,
        private readonly ?string $details = null
    ) {
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['info_type'],
            $array['headline'],
            $array['information_origin'],
            $array['modified_timestamp'],
            $array['label'],
            $array['value'],
            $array['details']
        );
    }

    public function getHeadline(): string
    {
        return $this->headline;
    }

    public function getInformationOrigin(): string
    {
        return $this->information_origin;
    }

    public function getModifiedTimestamp(): int
    {
        return $this->modified_timestamp;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getInfoType(): string
    {
        return $this->info_type;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }
    public function toArray(): array
    {
        return
        [
            'info_type' => $this->info_type ?? self::INFO_TYPE_ID,
            'headline' => $this->headline,
            'information_origin' => $this->information_origin,
            'modified_timestamp' => $this->modified_timestamp,
            'label' => $this->label,
            'value' => $this->value,
            'details' => $this->details
        ];
    }
}
