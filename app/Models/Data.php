<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'moduleId',
        'moduleName',
        'startDate',
        'examDate',
        'completedHours',
        'weeklyProgress',
        'totalHours',
        'weeklyHours',
        'remainingHours',
        'customSessionDates'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'startDate' => 'date',
        'examDate' => 'date',
        'completedHours' => 'float',
        'totalHours' => 'float',
        'weeklyHours' => 'float',
        'remainingHours' => 'float',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}