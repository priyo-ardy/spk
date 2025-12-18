<?php

namespace App\Constants;

/**
 * SPK Flow Status Constants
 * Menggantikan magic numbers untuk flow status SPK
 * 
 * @see app/Database/Migrations/2025-08-05-040706_SPK.php
 */
class FlowStatus
{
    /** Belum dikonfirmasi */
    public const UNCONFIRMED = '0';

    /** Dikonfirmasi oleh Mold Engineer */
    public const MOLD_CONFIRMED = '1';

    /** Dikonfirmasi oleh Planner */
    public const PLANNER_CONFIRMED = '2';

    /** Dikonfirmasi oleh ME (Manufacturing Engineer) */
    public const ME_CONFIRMED = '3';

    /** Selesai dikerjakan oleh Mold Engineer */
    public const MOLD_FINISHED = '4';

    /** Selesai dikerjakan oleh ME */
    public const ME_FINISHED = '5';

    /** Dikonfirmasi oleh Quality */
    public const QUALITY_CONFIRMED = '6';

    /** Proses selesai/closed */
    public const CLOSED = '7';

    /**
     * Get human-readable flow status name
     * 
     * @param string $status Flow status code
     * @return string Flow status name
     */
    public static function getName(string $status): string
    {
        return match ($status) {
            self::UNCONFIRMED => 'Un-Confirmed',
            self::MOLD_CONFIRMED => 'Confirmed by Mold Engineer',
            self::PLANNER_CONFIRMED => 'Confirmed by Planner',
            self::ME_CONFIRMED => 'Confirmed by ME',
            self::MOLD_FINISHED => 'Finished by Mold Engineer',
            self::ME_FINISHED => 'Finished by ME',
            self::QUALITY_CONFIRMED => 'Confirmed by Quality',
            self::CLOSED => 'Closed',
            default => 'Unknown',
        };
    }

    /**
     * Get all flow statuses as array for dropdown/select
     * 
     * @return array<string, string>
     */
    public static function getAll(): array
    {
        return [
            self::UNCONFIRMED => self::getName(self::UNCONFIRMED),
            self::MOLD_CONFIRMED => self::getName(self::MOLD_CONFIRMED),
            self::PLANNER_CONFIRMED => self::getName(self::PLANNER_CONFIRMED),
            self::ME_CONFIRMED => self::getName(self::ME_CONFIRMED),
            self::MOLD_FINISHED => self::getName(self::MOLD_FINISHED),
            self::ME_FINISHED => self::getName(self::ME_FINISHED),
            self::QUALITY_CONFIRMED => self::getName(self::QUALITY_CONFIRMED),
            self::CLOSED => self::getName(self::CLOSED),
        ];
    }

    /**
     * Check if SPK can be un-approved
     * Only possible when flow status is still unconfirmed
     * 
     * @param string $flowStatus Current flow status
     * @return bool
     */
    public static function canUnapprove(string $flowStatus): bool
    {
        return $flowStatus === self::UNCONFIRMED;
    }

    /**
     * Check if flow is completed
     * 
     * @param string $flowStatus Current flow status
     * @return bool
     */
    public static function isCompleted(string $flowStatus): bool
    {
        return $flowStatus === self::CLOSED;
    }
}
