<?php

namespace Spatie\Health\ResultStores\Report;

class ReportedCheck
{
    public static function make(
        string $name,
        string $message = '',
        string $status = '',
        array $meta = [],
    ): self {
        return new static(...func_get_args());
    }

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

    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

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
