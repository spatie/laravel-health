<?php

namespace Spatie\Health\ResultStores\StoredCheckResults;

class StoredCheckResult
{
    /**
     * @param string $name
     * @param string $message
     * @param string $status
     * @param array<string, mixed> $meta
     *
     * @return self
     */
    public static function make(
        string $name,
        string $message = '',
        string $status = '',
        array $meta = [],
    ): self {
        return new self(...func_get_args());
    }

    /**
     * @param string $name
     * @param string $message
     * @param string $status
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string $name,
        public string $message = '',
        public string $status = '',
        public array $meta = [],
    ) {
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array<string, mixed> $meta
     *
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
            'message' => $this->message,
            'status' => $this->status,
            'meta' => $this->meta,
        ];
    }
}
