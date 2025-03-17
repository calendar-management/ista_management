<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacance extends Model
{
    use HasFactory;
    
    protected $table = 'vacances';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'description_vacance',
        'type',
        'date_debut',
        'date_fin',
        'id_group',
        'id_fillier',
    ];
    
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];
    

}
