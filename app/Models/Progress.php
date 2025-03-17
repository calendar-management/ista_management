<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_progress';
    protected $fillable = [
        'id_teaching',
        'hours_completed',
        'remaining_hours',
        'final_exam_date',
        'module_start_date',
        'hours_affected'
    ];

    protected $casts = [
        'hours_affected' => 'json',
        'final_exam_date' => 'date',
        'module_start_date' => 'date'
    ];

    public function teaching()
    {
        return $this->belongsTo(Teaching::class, 'id_teaching');
    }

    public function weeklyProgress()
    {
        return $this->hasMany(ProgressWeekly::class, 'id_progress');
    }

    public function sessionDates()
    {
        return $this->hasMany(CustomSessionDate::class, 'id_progress');
    }
}
