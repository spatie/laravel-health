<?php

namespace Spatie\Health;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Spatie\Health\Checks\Check;
use Spatie\Health\Exceptions\DuplicateCheckNamesFound;
use Spatie\Health\Exceptions\InvalidCheck;
use Spatie\Health\Exceptions\InvalidTheme;
use Spatie\Health\ResultStores\ResultStore;
use Spatie\Health\ResultStores\ResultStores;

class Health
{
    /** @var array<int, Check> */
    protected array $checks = [];

    /** @var array<int, string> */
    public array $inlineStylesheets = [];
    
    protected ?string $theme = null;

    /** @param array<int, Check> $checks */
    public function checks(array $checks): self
    {
        $this->ensureCheckInstances($checks);

        $this->checks = array_merge($this->checks, $checks);

        $this->guardAgainstDuplicateCheckNames();

        return $this;
    }

    public function clearChecks(): self
    {
        $this->checks = [];

        return $this;
    }

    /** @return Collection<int, Check> */
    public function registeredChecks(): Collection
    {
        return collect($this->checks);
    }

    /** @return Collection<int, ResultStore> */
    public function resultStores(): Collection
    {
        return ResultStores::createFromConfig();
    }

    public function inlineStylesheet(string $stylesheet): self
    {
        $this->inlineStylesheets[] = $stylesheet;

        return $this;
    }

    public function assets(): HtmlString
    {
        $assets = [];

        foreach ($this->inlineStylesheets as $inlineStylesheet) {
            $assets[] = "<style>{$inlineStylesheet}</style>";
        }

        return new HtmlString(implode('', $assets));
    }
    
    public function setTheme(?string $theme): self
    {
        $this->theme = $theme ?? config('health.theme');

        $this->validateTheme();

        return $this;
    }

    protected function validateTheme(): void
    {
        if (! in_array($this->theme, ['light', 'dark'], true)) {
            throw InvalidTheme::themeIsInvalid($this->theme);
        }
    }

    public function getTheme(): string
    {
        return $this->theme ?? config('health.theme');
    }

    /** @param array<int,mixed> $checks */
    protected function ensureCheckInstances(array $checks): void
    {
        foreach ($checks as $check) {
            if (! $check instanceof Check) {
                throw InvalidCheck::doesNotExtendCheck($check);
            }
        }
    }

    protected function guardAgainstDuplicateCheckNames(): void
    {
        $duplicateCheckNames = collect($this->checks)
            ->map(fn (Check $check) => $check->getName())
            ->duplicates();

        if ($duplicateCheckNames->isNotEmpty()) {
            throw DuplicateCheckNamesFound::make($duplicateCheckNames);
        }
    }
}
