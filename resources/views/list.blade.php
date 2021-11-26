<html lang="en">
<head>
    <title>Health results</title>
</head>
<body>
@if(count($checkResults?->storedCheckResults ?? []))
    <div class="ml-1 mb-1">
        Last ran all the checks {{ $lastRanAt->diffForHumans() }}.
    </div>
    @foreach($checkResults->storedCheckResults as $result)
        <div>
            {{ $result->label }}
        </div>
    @endforeach
@endif
</body>
</html>
