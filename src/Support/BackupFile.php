<?php

namespace Spatie\Health\Support;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class BackupFile
{
    protected ?SymfonyFile $file = null;

    public function __construct(
        protected string $path,
        protected ?Filesystem $disk = null,
        protected ?string $parseModifiedUsing = null,
    ) {
        if (! $disk) {
            $this->file = new SymfonyFile($path);
        }
    }

    public function path(): string
    {
        return $this->path;
    }

    public function size(): int
    {
        return $this->file ? $this->file->getSize() : $this->disk->size($this->path);
    }

    public function lastModified(): ?int
    {
        if ($this->parseModifiedUsing) {
            $filename = Str::of($this->path)->afterLast('/')->before('.');

            try {
                return (int) Carbon::createFromFormat($this->parseModifiedUsing, $filename)->timestamp;
            } catch (InvalidFormatException $e) {
                return null;
            }
        }

        return $this->file ? $this->file->getMTime() : $this->disk->lastModified($this->path);
    }
}
