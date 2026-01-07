<?php

namespace LaraModule\Core\Providers;

use Illuminate\Support\Facades\File;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');
        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

		if(app()->runningInConsole()) {
			config(['telescope.enabled' => false]);
		}

        // Auto-create symlink to Telescope assets to avoid publishing
        $this->createTelescopeSymlink();
    }

    protected function createTelescopeSymlink(): void
    {
        $packageAssets = __DIR__.'/../Public/telescope';
        $publicTarget = public_path('vendor/telescope');

        if (! file_exists($publicTarget) || ! is_link($publicTarget)) {
            if (is_dir($publicTarget) && ! is_link($publicTarget)) {
                File::deleteDirectory($publicTarget);
            }

            if (! is_dir(dirname($publicTarget))) {
                mkdir(dirname($publicTarget), 0755, true);
            }

            $relativePath = $this->getRelativePath($publicTarget, $packageAssets);
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows requires different symlink handling
                @symlink($packageAssets, $publicTarget);
            } else {
                @symlink($relativePath, $publicTarget);
            }
        }
    }

    /**
     * Get relative path from target to source.
     */
    protected function getRelativePath(string $from, string $to): string
    {
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $fromParts = explode('/', dirname($from));
        $toParts = explode('/', $to);

        // Find common base
        $common = [];
        for ($i = 0; $i < min(count($fromParts), count($toParts)); $i++) {
            if ($fromParts[$i] === $toParts[$i]) {
                $common[] = $fromParts[$i];
            } else {
                break;
            }
        }

        $commonCount = count($common);
        $fromRemaining = array_slice($fromParts, $commonCount);
        $toRemaining = array_slice($toParts, $commonCount);

        $relativeParts = array_fill(0, count($fromRemaining), '..');
        $relativeParts = array_merge($relativeParts, $toRemaining);

        return implode('/', $relativeParts);
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);
        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }
}
