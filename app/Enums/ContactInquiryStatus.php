<?php

namespace App\Enums;

enum ContactInquiryStatus: string
{
    case New = 'new';
    case Reviewed = 'reviewed';
    case Contacted = 'contacted';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Reviewed => 'Reviewed',
            self::Contacted => 'Contacted',
            self::Closed => 'Closed',
        };
    }
}
