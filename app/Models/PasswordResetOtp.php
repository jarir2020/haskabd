<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['email', 'otp', 'verified', 'expires_at', 'next_request_at'];

    protected $dates = ['expires_at', 'next_request_at'];

    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function canRequestNew(): bool
    {
        return now()->gt($this->next_request_at);
    }
}
