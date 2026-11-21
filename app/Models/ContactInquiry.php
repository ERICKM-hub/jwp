<?php

namespace App\Models;

use App\Enums\ContactInquiryStatus;
use Database\Factories\ContactInquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'phone',
    'organization',
    'subject',
    'message',
    'status',
    'submitted_at',
])]
class ContactInquiry extends Model
{
    /** @use HasFactory<ContactInquiryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ContactInquiryStatus::class,
            'submitted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ContactInquiry $inquiry): void {
            if ($inquiry->status === null) {
                $inquiry->status = ContactInquiryStatus::New;
            }

            if ($inquiry->submitted_at === null) {
                $inquiry->submitted_at = now();
            }
        });
    }

    public function scopeStatus(Builder $query, ContactInquiryStatus|string $status): Builder
    {
        $value = $status instanceof ContactInquiryStatus ? $status->value : $status;

        return $query->where('status', $value);
    }

    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderByDesc('submitted_at')->orderByDesc('id');
    }
}
