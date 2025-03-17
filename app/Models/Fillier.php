<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fillier extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_fillier';
    protected $fillable = ['name', 'code_fillier'];

    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'id_fillier');
    }
}
