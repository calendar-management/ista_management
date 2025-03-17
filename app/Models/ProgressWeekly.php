<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressWeekly extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_weekly';
    protected $fillable = [
        'id_progress',
        'week',
        'status'
    ];

    public function progress()
    {
        return $this->belongsTo(Progress::class, 'id_progress');
    }
}
