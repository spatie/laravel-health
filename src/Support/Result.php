<?php

namespace Spatie\Health\Support;

use Carbon\Carbon;
use Spatie\Health\Checks\Check;
use Spatie\Health\Enums\Status;

class Result
{
    /** @var array<int, mixed> */
    public array $meta = [];

    public Check $check;

    public ?Carbon $ended_at;

    public static function make(string $message = ''): self
    {
        return new self(Status::ok(), $message);
    }

    public function __construct(
        public Status  $status,
        public string $message = ''
    ) {
    }

    public function check(Check $check): self
    {
        $this->check = $check;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return trans($this->message, $this->meta);
    }

    public function ok(string $message = ''): self
    {
        $this->message = $message;

        $this->status = Status::ok();

        return $this;
    }

    public function warning(string $message = ''): self
    {
        $this->message = $message;

        $this->status = Status::warning();

        return $this;
    }

    public function failed(string $message = ''): self
    {
        $this->message = $message;

        $this->status = Status::failed();

        return $this;
    }

    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function endedAt(Carbon $carbon): self
    {
        $this->ended_at = $carbon;

        return $this;
    }
}
