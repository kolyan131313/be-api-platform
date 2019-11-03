<?php declare(strict_types=1);

namespace App\Enum;

class VerificationStatusEnum
{
    public const REQUESTED = 0;
    public const APPROVED = 1;
    public const REJECTED = 2;

    public static function getLabels(): array
    {
        return [
            self::REQUESTED => 'Requested',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        ];
    }

    /**
     * Check if is finished status
     *
     * @param int $status
     *
     * @return bool
     */
    public static function isFinishedStatus(int $status): bool
    {
        return in_array($status, [self::APPROVED, self::REJECTED]);
    }
}