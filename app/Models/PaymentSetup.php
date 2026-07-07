<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PaymentSetup extends Model
{
    public const TERM_OPTIONS = [
        'All',
        '1st Term',
        '2nd Term',
        '3rd Term',
    ];

    public const LEGACY_TERM_OPTIONS = [
        'Term 1',
        'Term 2',
        'Term 3',
    ];

    protected $fillable = [
        'payment_type',
        'level',
        'term',
        'amount',
        'effective_date',
        'last_updated',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'last_updated' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public static function normaliseTerm(?string $term): string
    {
        return match (trim((string) $term)) {
            'Term 1', 'First Term', 'first term', '1st term' => '1st Term',
            'Term 2', 'Second Term', 'second term', '2nd term' => '2nd Term',
            'Term 3', 'Third Term', 'third term', '3rd term' => '3rd Term',
            '1st Term', '2nd Term', '3rd Term' => trim((string) $term),
            default => 'All',
        };
    }

    public static function levelOptionsFor(?string $classLevel): array
    {
        $level = trim((string) $classLevel);

        return match ($level) {
            'JSS', 'SS' => array_values(array_unique([$level, 'Secondary', 'All'])),
            'Secondary' => ['Secondary', 'JSS', 'SS', 'All'],
            'Primary' => ['Primary', 'All'],
            'Nursery' => ['Nursery', 'All'],
            'All', '' => ['All'],
            default => array_values(array_unique([$level, 'All'])),
        };
    }

    public static function termOptionsFor(?string $term): array
    {
        $normalised = self::normaliseTerm($term);

        $legacy = match ($normalised) {
            '1st Term' => 'Term 1',
            '2nd Term' => 'Term 2',
            '3rd Term' => 'Term 3',
            default => null,
        };

        return array_values(array_filter(array_unique([$normalised, $legacy, 'All'])));
    }

    public static function schoolFeeFor(?string $classLevel, ?string $term = null): ?self
    {
        $levelOptions = self::levelOptionsFor($classLevel);
        $termOptions = self::termOptionsFor($term);

        return self::where('payment_type', 'School Fees')
            ->where('status', 'Active')
            ->whereIn('level', $levelOptions)
            ->whereIn('term', $termOptions)
            ->get()
            ->sortBy(fn (self $setup) => self::setupPriority($setup, $levelOptions, $termOptions))
            ->first();
    }

    public static function activeSchoolFees(): Collection
    {
        return self::where('payment_type', 'School Fees')
            ->where('status', 'Active')
            ->get();
    }

    private static function setupPriority(self $setup, array $levelOptions, array $termOptions): int
    {
        $levelPriority = array_search($setup->level, $levelOptions, true);
        $termPriority = array_search($setup->term, $termOptions, true);

        return (($levelPriority === false ? 99 : $levelPriority) * 10)
            + ($termPriority === false ? 9 : $termPriority);
    }
}
