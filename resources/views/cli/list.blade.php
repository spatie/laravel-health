<div class="my-1">
    @if(count($checkResults->storedCheckResults))
        <div class="ml-1 underline mb-1">Check results</div>

        <div class="ml-1 mb-1">
            Last ran all the checks {{ $lastRanAt->diffForHumans() }}.
        </div>

        <table style="box">
            <thead>
            <tr>
                <td></td>
                <td>Check</td>
                <td>Summary</td>
                <td>Error message</td>
            </tr>
            </thead>
            @foreach($checkResults->storedCheckResults as $result)
                <tr>
                    <td class="{{ $color($result->status) }}"></td>
                    <td>{{ $result->label }}</td>
                    <td>{{ $result->shortSummary }}</td>
                    <td>{{ $result->notificationMessage }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="ml-1">
        No checks have run yet...<br/>
        Please execute:

        php artisan health:run-checks
        </div>
    @endif
</div>
