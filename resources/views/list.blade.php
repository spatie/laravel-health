<html lang="en" class="{{$theme == 'dark' ? 'dark' : ''}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ __('health::notifications.health_results') }}</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    {{$assets}}
</head>

<body class="antialiased bg-gray-100 mt-7 md:mt-12 dark:bg-gray-900">
<div class="mx-auto max-w-7xl lg:px-8 sm:px-6">
    <div class="flex flex-wrap justify-center space-y-3">
        <h4 class="w-full text-2xl font-bold text-center text-gray-900 dark:text-white">{{ __('health::notifications.laravel_health') }}</h4>
        <div class="flex justify-center w-full">
            <x-health-logo/>
        </div>
        @if ($lastRanAt)
            <div class="{{ $lastRanAt->diffInMinutes() > 5 ? 'text-red-400' : 'text-gray-400 dark:text-gray-500' }} text-sm text-center font-medium">
                {{ __('health::notifications.check_results_from') }} {{ $lastRanAt->diffForHumans() }}
            </div>
        @endif
    </div>
    <div class="px-2 mt-6 md:mt-8 md:px-0">
        @if (count($checkResults?->storedCheckResults ?? []))
            <dl class=" grid grid-cols-1 gap-2.5 sm:gap-3 md:gap-5 md:grid-cols-6 ">
                @foreach (collect($checkResults->storedCheckResults)->groupBy(fn(Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult $r) => $r->serverKey) as $serverKey => $results)

                    <details class="accordion">
                        <summary style="margin-bottom: 10px;"
                                 class="flex items-start px-4 space-x-2 overflow-hidden  text-opacity-0 transition transform bg-white shadow-md shadow-gray-200 dark:shadow-black/25 dark:shadow-md dark:bg-gray-800 rounded-xl sm:p-6   dark:border-t dark:border-gray-700">
                            <h1 class=" font-bold text-gray-900 dark:text-white">
                                {{ $serverKey }}
                            </h1>
                        </summary>
                        @foreach ($results as $result)
                            <div style="margin-bottom: 10px;"
                                 class="flex items-start px-4 space-x-4 overflow-hidden py-5 text-opacity-0 transition transform bg-white shadow-md shadow-gray-200 dark:shadow-black/25 dark:shadow-md dark:bg-gray-800 rounded-xl   dark:border-t dark:border-gray-700">
                                <x-health-status-indicator :result="$result"/>
                                <div>
                                    <dd class=" font-bold text-gray-900 dark:text-white  md:text-xl">
                                        {{ $result->label }}
                                    </dd>
                                    <dt class=" text-sm font-medium text-gray-600 dark:text-gray-300 md:mt-1">
                                        @if (!empty($result->notificationMessage))
                                            {{ $result->notificationMessage }}
                                        @else
                                            {{ $result->shortSummary }}
                                        @endif
                                    </dt>
                                </div>
                            </div>
                        @endforeach
                    </details>

                @endforeach
            </dl>
        @endif
    </div>
</div>
</body>
</html>
