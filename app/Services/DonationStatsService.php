<?php

namespace App\Services;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DonationStatsService
{
    private const CACHE_KEY = 'donation:stats:v3';

    private const CACHE_TTL_SECONDS = 60;

    private const LOCK_KEY = 'donation:stats:lock';

    private const LOCK_SECONDS = 5;

    private const LOCK_WAIT_SECONDS = 2;

    /**
     * @return array{active_donors: int, total_success_donations: int}
     */
    public function getStats(): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached)) {
            return $this->normalizeStats($cached);
        }

        try {
            return Cache::lock(self::LOCK_KEY, self::LOCK_SECONDS)->block(
                self::LOCK_WAIT_SECONDS,
                function (): array {
                    $cached = Cache::get(self::CACHE_KEY);

                    if (is_array($cached)) {
                        return $this->normalizeStats($cached);
                    }

                    $stats = $this->readFromStore();
                    Cache::put(self::CACHE_KEY, $stats, self::CACHE_TTL_SECONDS);

                    return $stats;
                }
            );
        } catch (LockTimeoutException) {
            return $this->readFromStore();
        }
    }

    public function recordSuccessfulDonation(?int $userId): void
    {
        DB::table('platform_stats')
            ->where('id', 1)
            ->increment('total_success_donations');

        if ($userId === null || ! $this->isVerifiedDonor($userId)) {
            return;
        }

        $inserted = DB::affectingStatement(
            'INSERT IGNORE INTO active_donor_markers (user_id, first_donated_at) VALUES (?, ?)',
            [$userId, now()]
        );

        if ($inserted > 0) {
            DB::table('platform_stats')
                ->where('id', 1)
                ->increment('active_donors');
        }
    }

    /**
     * @param array<string, mixed> $stats
     * @return array{active_donors: int, total_success_donations: int}
     */
    private function normalizeStats(array $stats): array
    {
        return [
            'active_donors' => (int) ($stats['active_donors'] ?? 0),
            'total_success_donations' => (int) ($stats['total_success_donations'] ?? 0),
        ];
    }

    /**
     * @return array{active_donors: int, total_success_donations: int}
     */
    private function readFromStore(): array
    {
        $row = DB::table('platform_stats')
            ->where('id', 1)
            ->first(['active_donors', 'total_success_donations']);

        return [
            'active_donors' => (int) ($row->active_donors ?? 0),
            'total_success_donations' => (int) ($row->total_success_donations ?? 0),
        ];
    }

    private function isVerifiedDonor(int $userId): bool
    {
        return DB::table('users')
            ->where('id', $userId)
            ->where('role', 'donor')
            ->where('is_verified', true)
            ->exists();
    }
}
