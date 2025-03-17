<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomSessionDate extends Model
{
    use HasFactory;

    protected $table = 'custom_session_dates';

    protected $fillable = [
        'id_progress',
        'week_index',
        'session_date'
    ];

    public function progress()
    {
        return $this->belongsTo(Progress::class, 'id_progress');
    }
}

