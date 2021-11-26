<div class="m-1">
    @if(count($checkResults->storedCheckResults))
        <div class="underline mb-1">Check results</div>

        <div class="mb-1">
            Last ran all the checks {{ $lastRanAt->diffForHumans() }}
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
        No checks have run yet...<br/>
        Please execute:

        php artisan health:run-checks
    @endif
</div>
