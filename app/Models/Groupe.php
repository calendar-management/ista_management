<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;
    
    protected $table = 'groupes';
    protected $primaryKey = 'id_group';
    
    protected $fillable = [
        'name',
        'id_fillier',
        'niveau',
        'effectif',
        'etablissement',
    ];
    
    public function fillier()
    {
        return $this->belongsTo(Fillier::class, 'id_fillier','id_fillier');
    }
    
    public function teachings()
    {
        return $this->hasMany(Teaching::class, 'id_group');
    }
    

}