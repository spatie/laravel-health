<div class="mx-2 my-1">
    @if(count($checkResults?->storedCheckResults ?? []))
        <div class="w-full text-white text-center bg-blue-800"></div>
        <div class="w-full text-white bg-blue-800">
            <span class="p-2 text-left w-1/2">Laravel Health Check Results</span>
            <span class="p-2 text-right w-1/2">
               Last ran all the checks
                @if ($lastRanAt->diffInMinutes() < 1)
                    just now
                @else
                    {{ $lastRanAt->diffForHumans() }}
                @endif
        </span>
        </div>
        <div class="w-full text-white text-center bg-blue-800 mb-1"></div>

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
                    <td class="{{ $color($result->status) }}">{{ ucfirst($result->status) }}</td>
                    <td>{{ $result->label }}</td>
                    <td>{{ $result->shortSummary }}</td>
                    <td>{{ $result->notificationMessage }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <div>
            No checks have run yet...<br/>
            Please execute this command:

            php artisan health:check
        </div>
    @endif
</div>
