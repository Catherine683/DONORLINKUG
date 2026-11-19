<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $email
 * @property string $code
 * @property string $purpose
 * @property Carbon $expires_at
 * @property Carbon|null $used_at
 * @property int $attempts
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class EmailOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'purpose',
        'expires_at',
        'used_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
}
