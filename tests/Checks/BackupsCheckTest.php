<?php

use Spatie\Health\Checks\Checks\BackupsCheck;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\TemporaryDirectory\TemporaryDirectory;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    $this->backupsCheck = BackupsCheck::new();

    Health::checks([
        BackupsCheck::new(),
    ]);

    $this->temporaryDirectory = TemporaryDirectory::make(getTemporaryDirectory())->force()->empty();

    testTime()->freeze('2024-01-01 00:00:00');

});

it('it will succeed if a file with the given glob exist', function () {
    addTestFile($this->temporaryDirectory->path('hey.zip'));

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->run();

    expect($result)->status->toBe(Status::ok());
});

it('it will fail if a file with the given glob does not exist', function () {
    addTestFile($this->temporaryDirectory->path('hey.other'));

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->run();

    expect($result)->status->toBe(Status::failed());
});

it('will fail if the given directory does not exist', function () {
    $result = $this->backupsCheck
        ->locatedAt('non-existing-directory')
        ->run();

    expect($result)->status->toBe(Status::failed());
});

it('will fail if the backup is smaller than the given size', function () {
    addTestFile($this->temporaryDirectory->path('hey.zip'), sizeInMb: 4);

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->atLeastSizeInMb(5)
        ->run();

    expect($result)->status->toBe(Status::failed());
});

it('will pass if the backup is at least than the given size', function (int $sizeInMb) {
    addTestFile($this->temporaryDirectory->path('hey.zip'), sizeInMb: $sizeInMb);

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->atLeastSizeInMb(5)
        ->run();

    expect($result)->status->toBe(Status::ok());
})->with([
    [5],
    [6],
]);

it('can check if the youngest backup is recent enough', function () {
    addTestFile($this->temporaryDirectory->path('hey.zip'));

    testTime()->addMinutes(4);

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->youngestBackShouldHaveBeenMadeBefore(now()->subMinutes(5)->startOfMinute())
        ->run();
    expect($result)->status->toBe(Status::ok());

    testTime()->addMinute();

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->youngestBackShouldHaveBeenMadeBefore(now()->subMinutes(5))
        ->run();
    expect($result)->status->toBe(Status::failed());
});

it('can check if the oldest backup is old enough', function () {
    addTestFile($this->temporaryDirectory->path('hey.zip'), date: now()->startOfMinute());

    testTime()->addMinutes(4);

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();

    expect($result)->status->toBe(Status::failed());

    testTime()->addMinute();

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();
    expect($result)->status->toBe(Status::failed());
});

it('can check that there are enough backups', function () {
    addTestFile($this->temporaryDirectory->path('first.zip'));

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->numberOfBackups(min: 2)
        ->run();
    expect($result)->status->toBe(Status::failed());

    addTestFile($this->temporaryDirectory->path('second.zip'));

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->numberOfBackups(min: 2)
        ->run();
    expect($result)->status->toBe(Status::ok());
});

it('can make sure that there are not too much backups', function () {
    addTestFile($this->temporaryDirectory->path('first.zip'));
    addTestFile($this->temporaryDirectory->path('second.zip'));

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->numberOfBackups(max: 2)
        ->run();
    expect($result)->status->toBe(Status::ok());

    addTestFile($this->temporaryDirectory->path('third.zip'));

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->numberOfBackups(max: 2)
        ->run();
    expect($result)->status->toBe(Status::failed());
});
