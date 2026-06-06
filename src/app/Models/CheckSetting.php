<?php

namespace App\Models;

use App\Enums\CheckMode;
use App\Enums\CheckMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interval',
        'timeout',
        'mode',
        'method',
        'starts_at',
        'is_running',
        'is_active',
        'domains_count',
        'checked_count',
    ];

    protected $casts = [
        'method' => CheckMethod::class,
        'mode' => CheckMode::class,
        'starts_at' => 'datetime',
        'is_running' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkLogs()
    {
        return $this->hasMany(CheckLog::class);
    }
}
