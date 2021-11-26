<html lang="en">
<head>
    <title>Health results</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<nav class="bg-white shadow">
    <div class="p-6 flex justify-between content-center items-baseline">
        <div class="text-2xl font-bold">
            Laravel Health
        </div>
        @if($lastRanAt)
            <div class="text-gray-400 text-sm">
                Check results from {{ $lastRanAt->diffForHumans() }}
            </div>
        @endif
    </div>
</nav>

<div class="m-6">
    @if(count($checkResults?->storedCheckResults ?? []))
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
            @foreach($checkResults->storedCheckResults as $result)
                @ray($result)
                <div
                    class="text-opacity-0 px-4 py-5 {{ $backgroundColor($result->status) }} shadow rounded-lg overflow-hidden sm:p-6">
                    <dd class="mt-1 text-xl mb-1 font-semibold {{ $textColor($result->status) }}">
                        {{ $result->label }}
                    </dd>
                    <dt class="text-sm font-medium {{ $textColor($result->status) }} opacity-50">
                        @if(! empty($result->notificationMessage))
                            {{ $result->notificationMessage }}
                        @else
                            {{ $result->shortSummary }}
                        @endif

                    </dt>
                </div>
            @endforeach
        </dl>
    @endif
</div>
</body>
</html>
