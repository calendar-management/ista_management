<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_module';

    /**
     * Get the teaching records for the module.
     */
    public function teaching()
    {
        return $this->hasMany(Teaching::class, 'id_module');
    }
}
