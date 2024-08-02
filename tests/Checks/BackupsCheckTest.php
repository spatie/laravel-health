<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Health\Checks\Checks\BackupsCheck;
use Spatie\Health\Enums\Status;
use Spatie\Health\Facades\Health;
use Spatie\Health\Support\BackupFile;
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

it('will pass if the backup is at least than the given size when loaded from filesystem disk', function (int $sizeInMb) {

    Storage::fake('backups');

    $tempFile = $this->temporaryDirectory->path('hey.zip');

    shell_exec("truncate -s {$sizeInMb}M {$tempFile}");

    Storage::disk('backups')->put('backups/hey.zip', file_get_contents($tempFile));

    $result = $this->backupsCheck
        ->onDisk('backups')
        ->locatedAt('backups')
        ->atLeastSizeInMb(5)
        ->run();

    expect($result)->status->toBe(Status::ok());
})->with([
    [5],
    [6],
]);

it('can check if the youngest backup is recent enough when loaded from filesystem disk', function () {

    Storage::fake('backups');
    Storage::disk('backups')->put('backups/hey.zip', 'content');

    testTime()->addMinutes(4);

    $result = $this->backupsCheck
        ->onDisk('backups')
        ->locatedAt('backups')
        ->youngestBackShouldHaveBeenMadeBefore(now()->subMinutes(5)->startOfMinute())
        ->run();

    expect($result)->status->toBe(Status::ok());

    testTime()->addMinutes(2);

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->youngestBackShouldHaveBeenMadeBefore(now()->subMinutes(5))
        ->run();
    expect($result)->status->toBe(Status::failed());
});

it('can check if the oldest backup is old enough when loaded from filesystem disk', function () {

    Storage::fake('backups');
    Storage::disk('backups')->put('backups/hey.zip', 'content');

    testTime()->addMinutes(4);

    $result = $this->backupsCheck
        ->onDisk('backups')
        ->locatedAt('backups')
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();

    expect($result)->status->toBe(Status::failed());

    testTime()->addMinutes(2);

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();
    expect($result)->status->toBe(Status::failed());
});

it('can parse modified time from file name', function ($format) {
    Storage::fake('backups');

    $now = now();
    Storage::disk('backups')->put('backups/'.$now->format($format).'.zip', 'content');

    $result1 = $this->backupsCheck
        ->onDisk('backups')
        ->locatedAt('backups')
        ->parseModifiedFormat($format)
        ->oldestBackShouldHaveBeenMadeAfter($now->subMinutes(5))
        ->run();

    testTime()->addMinutes(6);

    $backupFile = new BackupFile('backups/'.$now->format($format).'.zip', Storage::disk('backups'), $format);

    expect($backupFile->lastModified())->toBe($now->timestamp);

    $result2 = $this->backupsCheck
        ->onDisk('backups')
        ->locatedAt('backups')
        ->parseModifiedFormat($format)
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();

    expect($result1)->status->toBe(Status::failed())
        ->and($result2)->status->toBe(Status::ok());

    testTime()->addMinutes(2);

    $result = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->oldestBackShouldHaveBeenMadeAfter(now()->subMinutes(5))
        ->run();
    expect($result)->status->toBe(Status::failed());
})->with([
    ['Y-m-d_H-i-s'],
    ['Ymd_His'],
    ['YmdHis'],
    ['\B\a\c\k\u\p_Ymd_His'],
]);

it('can check the size of only the first and last backup files', function () {
    $now = now()->startOfMinute();

    addTestFile($this->temporaryDirectory->path('hey1.zip'), date: $now, sizeInMb: 5);
    addTestFile($this->temporaryDirectory->path('hey2.zip'), date: $now->addMinutes(10), sizeInMb: 10);
    addTestFile($this->temporaryDirectory->path('hey3.zip'), date: $now->addMinutes(20), sizeInMb: 5);

    $result1 = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->atLeastSizeInMb(9)
        ->run();

    $result2 = $this->backupsCheck
        ->locatedAt($this->temporaryDirectory->path('*.zip'))
        ->atLeastSizeInMb(9)
        ->onlyCheckSizeOnFirstAndLast()
        ->run();

    expect($result1)->status->toBe(Status::ok())
        ->and($result2)->status->toBe(Status::failed());
});
