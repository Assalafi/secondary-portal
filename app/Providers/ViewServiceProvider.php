<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SchoolSettings;
use App\Models\SessionTerm;

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
            
            // Get current session/term from SessionTerm (source of truth)
            $currentSessionTerm = SessionTerm::where('is_current', true)->first();
            $academicSession = $currentSessionTerm->academic_year ?? ($schoolSettings->academic_session ?? '2024/2025');
            $currentTerm = $currentSessionTerm->term_name ?? ($schoolSettings->current_term ?? 'First Term');
            
            // Default settings if none exist
            $settings = $schoolSettings ? [
                'school_name' => $schoolSettings->school_name ?? 'Secondary School Portal',
                'school_logo' => $schoolSettings->school_logo,
                'favicon' => $schoolSettings->favicon,
                'phone_number' => $schoolSettings->phone_number ?? 'N/A',
                'email' => $schoolSettings->email ?? 'info@school.edu',
                'website' => $schoolSettings->website ?? 'www.school.edu',
                'school_address' => $schoolSettings->school_address ?? 'School Address',
                'established_year' => $schoolSettings->established_year ?? date('Y'),
                'academic_session' => $academicSession,
                'current_term' => $currentTerm,
            ] : [
                'school_name' => 'Secondary School Portal',
                'school_logo' => null,
                'favicon' => null,
                'phone_number' => 'N/A',
                'email' => 'info@school.edu',
                'website' => 'www.school.edu',
                'school_address' => 'School Address',
                'established_year' => date('Y'),
                'academic_session' => $academicSession,
                'current_term' => $currentTerm,
            ];
            
            $view->with('globalSettings', $settings);
        });
    }
}
