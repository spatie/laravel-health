<?php

namespace Spatie\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class CachedServicesCheck extends Check
{
	public function run(): Result
	{
		$application = app();
		$result = Result::make();

		if (!$application->configurationIsCached()) {
			return $result->failed('Configs are not cached.');
		}

		if (!$application->routesAreCached()) {
			return $result->failed('Routes are not cached.');
		}

		if (!$application->eventsAreCached()) {
			return $result->failed('Events are not cached.');
		}

		return $result->ok();
	}
}
