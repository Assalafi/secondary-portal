<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SchoolSettings;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share school settings with all views
        View::composer('*', function ($view) {
            $schoolSettings = SchoolSettings::first();
            
            // Default settings if none exist
            $settings = $schoolSettings ? [
                'school_name' => $schoolSettings->school_name ?? 'Secondary School Portal',
                'school_logo' => $schoolSettings->school_logo,
                'phone_number' => $schoolSettings->phone_number ?? 'N/A',
                'email' => $schoolSettings->email ?? 'info@school.edu',
                'website' => $schoolSettings->website ?? 'www.school.edu',
                'school_address' => $schoolSettings->school_address ?? 'School Address',
                'established_year' => $schoolSettings->established_year ?? date('Y'),
                'academic_session' => $schoolSettings->academic_session ?? '2024/2025',
                'current_term' => $schoolSettings->current_term ?? 'First Term',
            ] : [
                'school_name' => 'Secondary School Portal',
                'school_logo' => null,
                'phone_number' => 'N/A',
                'email' => 'info@school.edu',
                'website' => 'www.school.edu',
                'school_address' => 'School Address',
                'established_year' => date('Y'),
                'academic_session' => '2024/2025',
                'current_term' => 'First Term',
            ];
            
            $view->with('globalSettings', $settings);
        });
    }
}
