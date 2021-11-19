<?php

use function Pest\Laravel\artisan;
use Spatie\Health\Commands\ListChecksCommand;

it('can list all the checks', function () {
    artisan(ListChecksCommand::class)->assertSuccessful();
});
