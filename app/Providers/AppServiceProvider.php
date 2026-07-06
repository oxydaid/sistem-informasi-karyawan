<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole() && Schema::hasTable('app_settings')) {
            $settingsData = cache()->rememberForever('app_settings', function () {
                $setting = AppSetting::firstOrCreate([], [
                    'app_name' => 'Skynet',
                    'primary_color' => '#0ea5e9',
                    'secondary_color' => '#334155',
                ]);

                return $setting->toArray();
            });
            View::share('appSettings', (object) $settingsData);
        }

        Livewire::listen('dehydrate', function ($component) {
            // Check for validation errors
            if (method_exists($component, 'getErrorBag')) {
                $errors = $component->getErrorBag();
                if ($errors->any()) {
                    foreach ($errors->all() as $error) {
                        $component->dispatch('toast', type: 'error', title: 'Validasi Gagal', message: $error);
                    }
                }
            }

            // Check for session flash messages
            if (session()->has('success')) {
                $component->dispatch('toast', type: 'success', message: session('success'));
            }
            if (session()->has('error')) {
                $component->dispatch('toast', type: 'error', message: session('error'));
            }
        });
    }
}
