<?php
namespace Spatie\Health\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class BackupFile {
    protected ?SymfonyFile $file = null;

    public function __construct(
        protected string $path,
        protected ?Filesystem $disk = null,
    ) {
        if (!$disk) {
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

    public function lastModified(): int
    {
        return $this->file ? $this->file->getMTime() : $this->disk->lastModified($this->path);
    }

}
