<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_group';
    protected $fillable = ['name', 'id_fillier', 'niveau', 'effectif'];

    // Relationship with Fillier
    public function fillier()
    {
        return $this->belongsTo(Fillier::class, 'id_fillier');
    }

    // Relationship with Teaching assignments
    public function teachings()
    {
        return $this->hasMany(Teaching::class, 'id_group');
    }

    // Get all formateurs teaching this group
    public function formateurs()
    {
        return $this->belongsToMany(User::class, 'teaching', 'id_group', 'id_user')
                    ->where('role', 'formateur');
    }

    // Get all modules being taught to this group
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'teaching', 'id_group', 'id_module');
    }

    // Get progress for all modules
    public function progress()
    {
        return $this->hasManyThrough(
            Progress::class,
            Teaching::class,
            'id_group',
            'id_teaching',
            'id_group',
            'id_teaching'
        );
    }

    // Scope to get groups assigned to a specific formateur
    public function scopeForFormateur($query, $formateurId)
    {
        return $query->whereHas('teachings', function($q) use ($formateurId) {
            $q->where('id_user', $formateurId);
        });
    }
}
