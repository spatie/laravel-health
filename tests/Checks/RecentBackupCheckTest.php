<?php

use Spatie\Health\Checks\Checks\RecentBackupCheck;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function() {
    $this->recentBackupCheck = RecentBackupCheck::new();

    Health::checks([
        RecentBackupCheck::new(),
    ]);

    $this->temporaryDirectory = TemporaryDirectory::make(getTemporaryDirectory())->force()->empty();

    testTime()->freeze('2024-01-01 00:00:00');

});

it('it will succeed if a file with the given glob exist', function() {
    addTestFile($this->temporaryDirectory->path('hey.zip'));

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->run();

    expect($result)->status->toBe(Status::ok());
});

it('it will fail if a file with the given glob does not exist', function() {
    addTestFile($this->temporaryDirectory->path('hey.other'));

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->run();

    expect($result)->status->toBe(Status::failed());
});

it('will fail if the given directory does not exist', function() {
    $result = $this->recentBackupCheck
        ->locatedAt('non-existing-directory')
        ->run();

    expect($result)->status->toBe(Status::failed());
});

it('will fail if the backup is smaller than the given size', function() {
    addTestFile($this->temporaryDirectory->path('hey.zip'), sizeInMb: 4);

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->atLeastSizeInMb(5)
        ->run();

    expect($result)->status->toBe(Status::failed());
});

it('will pass if the backup is at least than the given size', function(int $sizeInMb) {
    addTestFile($this->temporaryDirectory->path('hey.zip'), sizeInMb: $sizeInMb);

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->atLeastSizeInMb(5)
        ->run();

    expect($result)->status->toBe(Status::ok());
})->with([
    [5],
    [6],
]);

it('can check if the youngest backup is recent enough', function() {
    addTestFile($this->temporaryDirectory->path('hey.zip'));

    testTime()->addMinutes(4);

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->youngestBackShouldHaveBeenMadeBefore(now()->subMinutes(5)->startOfMinute())
        ->run();
    expect($result)->status->toBe(Status::ok());

    testTime()->addMinute();

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->youngestBackShouldHaveBeenMadeBefore(now()->subMinutes(5))
        ->run();
    expect($result)->status->toBe(Status::failed());
});

it('can check if the oldest backup is old enough', function() {
    addTestFile($this->temporaryDirectory->path('hey.zip'), date: now()->startOfMinute());

    testTime()->addMinutes(4);

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();

    expect($result)->status->toBe(Status::failed());

    testTime()->addMinute();

    $result = $this->recentBackupCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();
    expect($result)->status->toBe(Status::failed());
});



