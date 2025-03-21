<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fillier extends Model
{
    use HasFactory;
    
    protected $table = 'filliers';
    protected $primaryKey = 'id_fillier';
    
    protected $fillable = [
        'name',
        'code_fillier',
        'etablissement',
    ];
    
    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'id_fillier','id_fillier');
    }
    
    public function teachings()
    {
        return $this->hasMany(Teaching::class, 'id_fillier');
    }
}