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
        'etablissement'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(Groupe::class, 'id_group', 'id_group');
    }

    public function fillier()
    {
        return $this->belongsTo(Fillier::class, 'id_fillier', 'id_fillier');
    }
}
