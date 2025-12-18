<?php

namespace App\Constants;

/**
 * User Level Constants
 * Menggantikan magic numbers untuk level user
 * 
 * Digunakan untuk role-based access control di seluruh aplikasi
 */
class UserLevel
{
    /** Super Administrator - Full access */
    public const SUPER_ADMIN = '0';

    /** Administrator - Full access except system settings */
    public const ADMIN = '1';

    /** Planner - Access to planning features */
    public const PLANNER = '2';

    /** Mold Engineer - Access to mold-related features */
    public const MOLD_ENGINEER = '3';

    /** Quality - Access to quality inspection features */
    public const QUALITY = '4';

    /** Manufacturing Engineer (ME) */
    public const ME = '5';

    /**
     * Get human-readable level name
     * 
     * @param string $level User level code
     * @return string Level name
     */
    public static function getName(string $level): string
    {
        return match ($level) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::ADMIN => 'Administrator',
            self::PLANNER => 'Planner',
            self::MOLD_ENGINEER => 'Mold Engineer',
            self::QUALITY => 'Quality',
            self::ME => 'Manufacturing Engineer',
            default => 'Unknown',
        };
    }

    /**
     * Get all user levels as array for dropdown/select
     * 
     * @return array<string, string>
     */
    public static function getAll(): array
    {
        return [
            self::SUPER_ADMIN => self::getName(self::SUPER_ADMIN),
            self::ADMIN => self::getName(self::ADMIN),
            self::PLANNER => self::getName(self::PLANNER),
            self::MOLD_ENGINEER => self::getName(self::MOLD_ENGINEER),
            self::QUALITY => self::getName(self::QUALITY),
            self::ME => self::getName(self::ME),
        ];
    }

    /**
     * Check if user is an administrator (Super Admin or Admin)
     * 
     * @param string $level User level
     * @return bool
     */
    public static function isAdmin(string $level): bool
    {
        return in_array($level, [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * Check if user can delete SPK data
     * 
     * @param string $level User level
     * @return bool
     */
    public static function canDeleteSPK(string $level): bool
    {
        return in_array($level, [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * Check if user can approve SPK
     * 
     * @param string $level User level
     * @return bool
     */
    public static function canApproveSPK(string $level): bool
    {
        return in_array($level, [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * Check if user can access admin menu
     * 
     * @param string $level User level
     * @return bool
     */
    public static function canAccessAdminMenu(string $level): bool
    {
        return self::isAdmin($level);
    }

    /**
     * Get the appropriate DataTable view based on user level
     * 
     * @param string $level User level
     * @return string Table/View name
     */
    public static function getSPKTableView(string $level): string
    {
        return match ($level) {
            self::SUPER_ADMIN, self::ADMIN => 'vw_t_spk',
            self::MOLD_ENGINEER => 'vw_spk_mold_engineer',
            self::PLANNER => 'vw_t_spk_planer',
            default => 'vw_t_spk',
        };
    }
}
