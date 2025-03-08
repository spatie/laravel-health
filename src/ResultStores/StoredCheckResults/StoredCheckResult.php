<?php

namespace Spatie\Health\ResultStores\StoredCheckResults;

class StoredCheckResult
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public static function make(
        string $name,
        string $label = '',
        ?string $notificationMessage = '',
        string $shortSummary = '',
        string $status = '',
        array $meta = [],
    ): self {
        return new self(...func_get_args());
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public string $name,
        public string $label = '',
        public ?string $notificationMessage = '',
        public string $shortSummary = '',
        public string $status = '',
        public array $meta = [],
    ) {}

    public function notificationMessage(string $message): self
    {
        $this->notificationMessage = $message;

        return $this;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function shortSummary(string $shortSummary): self
    {
        $this->shortSummary = $shortSummary;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return $this
     */
    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'notificationMessage' => $this->notificationMessage,
            'shortSummary' => $this->shortSummary,
            'status' => $this->status,
            'meta' => $this->meta,
        ];
    }
}
