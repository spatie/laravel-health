<?php

use Spatie\Health\Commands\ListChecksCommand;
use function Pest\Laravel\artisan;

it('can list all the checks', function() {
    artisan(ListChecksCommand::class)->assertSuccessful();
});
