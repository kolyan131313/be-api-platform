<?php declare(strict_types=1);

namespace App\Enum;

class VerificationStatusEnum
{
    public const VERIFICATION_REQUESTED = 0;
    public const APPROVED = 1;
    public const DECLINED = 2;

    public static function getLabels(): array
    {
        return [
            self::VERIFICATION_REQUESTED => 'Verification requested',
            self::APPROVED => 'Approved',
            self::DECLINED => 'Declined',
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
        return in_array($status, [self::APPROVED, self::DECLINED]);
    }
}