<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'check_setting_id',
        'is_available',
        'response_code',
        'response_time',
        'error',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function checkSetting()
    {
        return $this->belongsTo(CheckSetting::class);
    }
}
