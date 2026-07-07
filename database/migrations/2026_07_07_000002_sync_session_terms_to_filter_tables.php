<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('session_terms')->where('term_name', 'First Term')->update(['term_name' => '1st Term']);
        DB::table('session_terms')->where('term_name', 'Second Term')->update(['term_name' => '2nd Term']);
        DB::table('session_terms')->where('term_name', 'Third Term')->update(['term_name' => '3rd Term']);

        $sessionTerms = DB::table('session_terms')->orderBy('academic_year')->orderBy('start_date')->get();

        foreach ($sessionTerms->groupBy('academic_year') as $academicYear => $terms) {
            $existing = DB::table('academic_sessions')->where('name', $academicYear)->first();
            $data = [
                'start_date' => $terms->min('start_date'),
                'end_date' => $terms->max('end_date'),
                'is_current' => $terms->contains(fn ($term) => (bool) $term->is_current),
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('academic_sessions')->where('id', $existing->id)->update($data);
            } else {
                DB::table('academic_sessions')->insert(array_merge($data, [
                    'name' => $academicYear,
                    'created_at' => now(),
                ]));
            }
        }

        $termNames = $sessionTerms->pluck('term_name')->unique()->values();
        foreach ($termNames as $termName) {
            $existing = DB::table('terms')->where('name', $termName)->first();
            $data = [
                'number' => $this->termNumber($termName),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('terms', 'is_current')) {
                $currentSessionTerm = $sessionTerms->first(fn ($term) => (bool) $term->is_current);
                $data['is_current'] = $currentSessionTerm?->term_name === $termName;
            }

            if ($existing) {
                DB::table('terms')->where('id', $existing->id)->update($data);
            } else {
                DB::table('terms')->insert(array_merge($data, [
                    'name' => $termName,
                    'created_at' => now(),
                ]));
            }
        }

        $current = DB::table('session_terms')->where('is_current', true)->first();
        if ($current && Schema::hasTable('school_settings')) {
            $settings = DB::table('school_settings')->first();
            $data = [
                'academic_session' => $current->academic_year,
                'current_term' => $current->term_name,
                'updated_at' => now(),
            ];

            if ($settings) {
                DB::table('school_settings')->where('id', $settings->id)->update($data);
            } else {
                DB::table('school_settings')->insert(array_merge($data, [
                    'created_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep synced academic_sessions/terms because they may be referenced by invoices, scores, and reports.
    }

    private function termNumber(string $termName): int
    {
        $term = strtolower($termName);

        return match (true) {
            str_contains($term, '1st'), str_contains($term, 'first') => 1,
            str_contains($term, '2nd'), str_contains($term, 'second') => 2,
            str_contains($term, '3rd'), str_contains($term, 'third') => 3,
            default => 1,
        };
    }
};
