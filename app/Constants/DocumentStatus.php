<?php

namespace App\Constants;

/**
 * SPK Document Status Constants
 * Menggantikan magic numbers untuk status dokumen SPK
 * 
 * @see app/Database/Migrations/2025-08-05-040706_SPK.php
 */
class DocumentStatus
{
    /** Status ketika dokumen baru dibuat */
    public const CREATED = '0';

    /** Status ketika dokumen sudah disubmit/under review */
    public const SUBMITTED = '1';

    /** Status ketika dokumen sudah diapprove */
    public const APPROVED = '2';

    /** Status on progress di Mold Engineer */
    public const IN_PROGRESS_MOLD = '3';

    /** Status on progress di Planner */
    public const IN_PROGRESS_PLANNER = '4';

    /** Status on progress di Quality */
    public const IN_PROGRESS_QUALITY = '5';

    /** Status dokumen di-hold */
    public const HOLD = '6';

    /** Status dokumen ditolak */
    public const REJECTED = '7';

    /** Status dokumen selesai/closed */
    public const CLOSED = '8';

    /**
     * Get human-readable status name
     * 
     * @param string $status Status code
     * @return string Status name
     */
    public static function getName(string $status): string
    {
        return match ($status) {
            self::CREATED => 'Created',
            self::SUBMITTED => 'Under Review',
            self::APPROVED => 'Approved',
            self::IN_PROGRESS_MOLD => 'On Progress in Mold',
            self::IN_PROGRESS_PLANNER => 'On Progress in Planner',
            self::IN_PROGRESS_QUALITY => 'On Progress in Quality',
            self::HOLD => 'Hold',
            self::REJECTED => 'Rejected',
            self::CLOSED => 'Closed',
            default => 'Unknown',
        };
    }

    /**
     * Get all statuses as array for dropdown/select
     * 
     * @return array<string, string>
     */
    public static function getAll(): array
    {
        return [
            self::CREATED => self::getName(self::CREATED),
            self::SUBMITTED => self::getName(self::SUBMITTED),
            self::APPROVED => self::getName(self::APPROVED),
            self::IN_PROGRESS_MOLD => self::getName(self::IN_PROGRESS_MOLD),
            self::IN_PROGRESS_PLANNER => self::getName(self::IN_PROGRESS_PLANNER),
            self::IN_PROGRESS_QUALITY => self::getName(self::IN_PROGRESS_QUALITY),
            self::HOLD => self::getName(self::HOLD),
            self::REJECTED => self::getName(self::REJECTED),
            self::CLOSED => self::getName(self::CLOSED),
        ];
    }

    /**
     * Check if document can be edited
     * 
     * @param string $status Current status
     * @return bool
     */
    public static function isEditable(string $status): bool
    {
        return in_array($status, [self::CREATED, self::HOLD]);
    }

    /**
     * Check if document can be submitted
     * 
     * @param string $status Current status
     * @return bool
     */
    public static function canSubmit(string $status): bool
    {
        return $status === self::CREATED;
    }

    /**
     * Check if document can be approved
     * 
     * @param string $status Current status
     * @return bool
     */
    public static function canApprove(string $status): bool
    {
        return $status === self::SUBMITTED;
    }
}
