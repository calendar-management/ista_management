<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    use HasFactory;
    
    protected $table = 'teaching';
    protected $primaryKey = 'id_teaching';
    
    protected $fillable = [
        'id_user',
        'id_group',
        'id_module',
        'id_fillier',
        'module_start_date',
        'final_exam_date',
        'creneau',
    ];
    
    protected $casts = [
        'final_exam_date' => 'date',
        'module_start_date' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    public function group()
    {
        return $this->belongsTo(Groupe::class, 'id_group');
    }
    
    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }
    
    public function fillier()
    {
        return $this->belongsTo(Fillier::class, 'id_fillier');
    }
    
    public function progress()
    {
        return $this->hasOne(Progress::class, 'id_teaching');
    }
}
