<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacance extends Model
{
    use HasFactory;
    
    protected $table = 'vacances';
    protected $primaryKey = 'id_vacance';
    
    protected $fillable = [
        'description_vacance',
        'type',
        'groupe',
        'filliere',
        'date_debut',
        'date_fin',
    ];
   
   
    
    
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];
    
    public function user()
    {
        // return $this->belongsTo(User::class, 'ajoute_par');
    }
}
