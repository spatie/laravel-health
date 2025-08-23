<?php

namespace Spatie\Health\ResultFormats;

use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResults;

class NagiosResultsFormat implements ResultsFormat
{
    public function format(StoredCheckResults|null $checkResults): string
    {
        $status = $this->determineStatus($checkResults);
        $perfData = $this->getPerformanceData($checkResults);
        $details = $this->getDetails($checkResults);

        $shortMessage = collect($checkResults->storedCheckResults)
            ->filter(function ($result) use ($status) {
                return $status === 'critical'
                    ? strtolower($result->status) === 'failed'
                    : strtolower($result->status) === 'warning';
            })
            ->map(function ($result) {
                return $result->notificationMessage ?? $result->shortSummary;
            })
            ->first() ?? sprintf("%d checks executed", count($checkResults->storedCheckResults));

        return sprintf(
            "%s: %s|%s\n%s",
            strtoupper($status),
            $shortMessage,
            $perfData,
            $details
        );
    }

    protected function determineStatus(StoredCheckResults $checkResults): string
    {
        $results = $checkResults->storedCheckResults;
        $hasWarning = false;

        foreach ($results as $result) {
            if (strtolower($result->status) === 'failed') {
                return 'critical';
            }
            if (strtolower($result->status) === 'warning') {
                $hasWarning = true;
            }
        }

        return $hasWarning ? 'warning' : 'ok';
    }

    protected function getPerformanceData(StoredCheckResults $checkResults): string
    {
        return collect($checkResults->storedCheckResults)
            ->map(function ($result) {
                if (empty($result->meta)) {
                    return null;
                }

                $label = strtolower($result->label);
                $value = $result->meta;

                if (!is_numeric($value)) {
                    if (is_array($value)) {
                        $value = array_values($value)[0] ?? null;
                    } else {
                        return null;
                    }
                }

                if (!is_numeric($value)) {
                    return null;
                }

                return sprintf("'%s'=%s", $label, $value);
            })
            ->filter()
            ->join(' ');
    }

    protected function getDetails(StoredCheckResults $checkResults): string
    {
        return collect($checkResults->storedCheckResults)
            ->map(function ($result) {
                $label = $result->label ?? 'Unknown Check';
                $shortSummary = $result->notificationMessage ?? $result->shortSummary ?? 'No details available';
                $status = strtoupper($result->status ?? 'UNKNOWN');

                return sprintf(
                    "%s: %s [%s]",
                    $label,
                    $shortSummary,
                    $status
                );
            })
            ->join("\n");
    }
}
