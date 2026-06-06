<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'is_available',
        'checked_at',
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
