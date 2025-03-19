<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groupes';
    protected $primaryKey = 'id_group';

    /**
     * Get the teaching records for the group.
     */
    public function teaching()
    {
        return $this->hasMany(Teaching::class, 'id_group');
    }
}
