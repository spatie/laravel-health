<?php

namespace Spatie\Health\ResultFormats;

use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

interface ResultsFormat
{
    public function format(StoredCheckResults $checkResults): string;
}
