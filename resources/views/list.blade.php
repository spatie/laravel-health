<html lang="en">
<head>
    <title>Health results</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 pt-8">
    <div class="relative flex items-top justify-center bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-md px-8 pb-8 pt-4 shadow mb-4">
                <div class="flex justify-between items-center">
                    <h4 class="text-center text-3xl font-semibold text-gray-700 mb-4">Laravel Health</h4>
                    @if ($lastRanAt)
                        <div class="text-gray-400 text-sm text-center">
                            Check results from {{ $lastRanAt->diffForHumans() }}
                        </div>
                    @endif
                </div>
                <div class="pt-8">
                    @if (count($checkResults?->storedCheckResults ?? []))
                        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                            @foreach ($checkResults->storedCheckResults as $result)
                                @ray($result)
                                <div
                                    class="text-opacity-0 px-4 py-5 {{ $backgroundColor($result->status) }} hover:shadow transition transform rounded overflow-hidden sm:p-6">
                                    <dd class="mt-1 text-xl mb-1 font-semibold {{ $textColor($result->status) }}">
                                        {{ $result->label }}
                                    </dd>
                                    <dt class="text-sm font-medium {{ $textColor($result->status) }} opacity-50">
                                        @if (!empty($result->notificationMessage))
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
            </div>
        </div>
    </div>

</body>
</html>
