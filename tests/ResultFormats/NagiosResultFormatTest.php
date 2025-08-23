<?php

namespace Spatie\Health\Tests\ResultFormats;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultFormats\NagiosResultsFormat;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

class NagiosResultFormatTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_it_formats_results_correctly()
    {
        $mockedResults = collect([
            (object) [
                'status' => Status::ok()->value,
                'label' => 'Database',
                'shortSummary' => 'Connection successful',
                'meta' => ['response_time' => '100ms'],
            ],
            (object) [
                'status' => Status::warning()->value,
                'label' => 'Disk Space',
                'shortSummary' => 'Usage at 85%',
                'meta' => ['used_space' => '85%'],
            ],
        ]);

        $mockedStoredCheckResults = $this->createMock(StoredCheckResults::class);
        $mockedStoredCheckResults->storedCheckResults = $mockedResults;

        $formatter = new NagiosResultsFormat();

        $output = $formatter->format($mockedStoredCheckResults);

        $expectedOutput = <<<EOT
WARNING: Usage at 85%|
Database: Connection successful [OK]
Disk Space: Usage at 85% [WARNING]
EOT;

        $this->assertEquals(trim($expectedOutput), trim($output));
    }

    /**
     * @throws Exception
     */
    public function test_it_handles_no_checks()
    {
        $mockedStoredCheckResults = $this->createMock(StoredCheckResults::class);
        $mockedStoredCheckResults->storedCheckResults = collect();

        $formatter = new NagiosResultsFormat();

        $output = $formatter->format($mockedStoredCheckResults);

        $expectedOutput = "OK: 0 checks executed|\n";

        $this->assertEquals($expectedOutput, $output);
    }
}
