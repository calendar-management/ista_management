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
        'hours_affected',
        'weekly_hours',
        'etablissement'
    ];
    
    public function teaching()
    {
        return $this->belongsTo(Teaching::class, 'id_teaching');
    }
    
    public function customSessionDates()
    {
        return $this->hasMany(CustomSessionDate::class, 'id_progress');
    }
}