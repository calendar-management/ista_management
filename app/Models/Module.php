<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    
    protected $table = 'modules';
    protected $primaryKey = 'id_module';
    
    protected $fillable = [
        'name',
        'hours',
        'regional',
    ];
    protected $casts = [
        'final_exam_date' => 'date',
        'module_start_date' => 'date',
    ];
    
    public function teachings()
    {
        return $this->hasMany(Teaching::class, 'id_module');
    }
}
