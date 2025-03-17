<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;
    
    protected $table = 'progress';
    protected $primaryKey = 'id_progress';
    
    protected $fillable = [
        'id_teaching',
        'hours_completed',
        'remaining_hours',
        'module_start_date',
        'final_exam_date',
        'hours_affected'
    ];
    
    public function teaching()
    {
        return $this->belongsTo(Teaching::class, 'id_teaching');
    }
    
    public function weeklyProgress()
    {
        return $this->hasMany(ProgressWeekly::class, 'id_progress');
    }
    public function customSessionDates()
    {
        return $this->hasMany(CustomSessionDate::class, 'id_progress');
    }
}