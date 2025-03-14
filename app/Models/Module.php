<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'filiere_id', 'total_hours', 'description'];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
