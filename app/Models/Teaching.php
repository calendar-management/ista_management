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
        'creneau',
        'type_seance'
    ];

    public function formateur()
    {
        return $this->belongsTo(User::class, 'id_user')->where('role', 'formateur');
    }

    public function groupe()
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
