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

    /**
     * $size and $lastModified may be passed when they are already known — a
     * directory listing returns both for every file, so supplying them avoids
     * a metadata request per file.
     */
    public function __construct(
        protected string $path,
        protected ?Filesystem $disk = null,
        protected ?string $parseModifiedUsing = null,
        protected ?int $size = null,
        protected ?int $lastModified = null,
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
        if ($this->size !== null) {
            return $this->size;
        }

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

        if ($this->lastModified !== null) {
            return $this->lastModified;
        }

        return $this->file ? $this->file->getMTime() : $this->disk->lastModified($this->path);
    }
}
